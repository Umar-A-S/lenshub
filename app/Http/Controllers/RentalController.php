<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Penalty;
use App\Models\Rental;
use App\Models\RentalItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Rental::with(['user', 'client', 'items.gear', 'penalty'])
                ->latest()
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'started_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:started_at'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.gear_id' => ['required', 'exists:gears,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $startedAt = Carbon::parse($data['started_at']);
        $endAt = Carbon::parse($data['end_at']);

        return DB::transaction(function () use ($user, $data, $startedAt, $endAt) {
            $rental = Rental::create([
                'user_id' => $user->id,
                'client_id' => $data['client_id'],
                'total_price' => 0,
                'status' => 'active',
                'started_at' => $startedAt,
                'end_at' => $endAt,
            ]);

            $totalPrice = 0;
            $durationHours = max(1, (int) ceil($startedAt->diffInMinutes($endAt) / 60));
            $useDayRate = $durationHours >= 24;
            $durationDays = max(1, (int) ceil($durationHours / 24));

            foreach ($data['items'] as $item) {
                $gear = Gear::lockForUpdate()->findOrFail($item['gear_id']);
                $quantity = (int) $item['quantity'];

                if ($gear->status === 'maintenance') {
                    abort(422, 'Gear sedang dalam status maintenance.');
                }

                if ($gear->stock_available < $quantity) {
                    abort(422, 'Stok gear tidak mencukupi.');
                }

                RentalItem::create([
                    'rental_id' => $rental->id,
                    'gear_id' => $gear->id,
                    'quantity' => $quantity,
                ]);

                $gear->decrement('stock_available', $quantity);

                $lineTotal = $useDayRate
                    ? $quantity * ($gear->price_per_day * $durationDays)
                    : $quantity * ($gear->price_per_hour * $durationHours);

                $totalPrice += $lineTotal;
            }

            $rental->update(['total_price' => $totalPrice]);

            return response()->json([
                'message' => 'Transaksi sewa berhasil dibuat.',
                'data' => $rental->load('client', 'items.gear'),
            ], 201);
        });
    }

    public function show(Rental $rental): JsonResponse
    {
        return response()->json($rental->load(['user', 'client', 'items.gear', 'penalty']));
    }

    public function update(Request $request, Rental $rental): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['sometimes', 'exists:clients,id'],
            'started_at' => ['sometimes', 'date'],
            'end_at' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:active,completed,late,canceled'],
        ]);

        $rental->update($data);

        return response()->json([
            'message' => 'Rental berhasil diperbarui.',
            'data' => $rental,
        ]);
    }

    public function destroy(Rental $rental): JsonResponse
    {
        if (in_array($rental->status, ['active', 'late'], true)) {
            foreach ($rental->items as $item) {
                $item->gear()->increment('stock_available', $item->quantity);
            }
        }

        $rental->delete();

        return response()->json([
            'message' => 'Rental berhasil dihapus.',
        ]);
    }

    public function returnRental(Request $request, Rental $rental): JsonResponse
    {
        if ($rental->returned_at) {
            return response()->json([
                'message' => 'Rental ini sudah pernah dikembalikan.',
            ], 422);
        }

        $data = $request->validate([
            'returned_at' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
        ]);

        $returnedAt = isset($data['returned_at'])
            ? Carbon::parse($data['returned_at'])
            : now();

        return DB::transaction(function () use ($rental, $returnedAt, $data) {
            $rental->load('items.gear');

            foreach ($rental->items as $item) {
                $item->gear()->increment('stock_available', $item->quantity);
            }

            $lateMinutes = 0;
            $penaltyAmount = 0;
            $status = 'completed';

            if ($returnedAt->greaterThan($rental->end_at)) {
                $lateMinutes = $rental->end_at->diffInMinutes($returnedAt);
                $penaltyAmount = (int) ceil($lateMinutes / 60) * 5000;
                $status = 'late';
            }

            $rental->update([
                'returned_at' => $returnedAt,
                'status' => $status,
            ]);

            if ($lateMinutes > 0 || $penaltyAmount > 0) {
                Penalty::updateOrCreate(
                    ['rental_id' => $rental->id],
                    [
                        'late_duration_minutes' => $lateMinutes,
                        'penalty_amount' => $penaltyAmount,
                        'reason' => $data['reason'] ?? 'Terlambat mengembalikan alat.',
                    ]
                );
            }

            return response()->json([
                'message' => 'Pengembalian berhasil diproses.',
                'data' => $rental->fresh(['client', 'items.gear', 'penalty']),
            ]);
        });
    }
}
