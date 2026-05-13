<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Gear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * GET /api/rentals
     * Mendapatkan daftar rental dengan filter
     */
    public function index(Request $request)
    {
        $query = Rental::with(['gear', 'user', 'gear.category']);

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan gear
        if ($request->has('gear_id')) {
            $query->where('gear_id', $request->gear_id);
        }

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $perPage = $request->get('per_page', 15);
        $rentals = $query->latest()->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar rental berhasil diambil',
            'data'    => $rentals
        ], 200);
    }

    /**
     * GET /api/rentals/{id}
     * Mendapatkan detail rental spesifik
     */
    public function show($id)
    {
        $rental = Rental::with(['gear', 'user', 'gear.category'])->find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        // Tambahkan penalty details ke response
        $data = $rental->toArray();
        $data['penalty_details'] = $rental->penalty_details;
        $data['grand_total'] = $rental->grand_total;

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail rental berhasil diambil',
            'data'    => $data
        ], 200);
    }

    /**
     * POST /api/rentals
     * Membuat rental baru (booking)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gear_id'        => 'required|exists:gears,id',
            'start_date'     => 'required|date|after_or_equal:today',
            'start_time'     => 'required|date_format:H:i',
            'duration'       => 'required|integer|min:1',
            'whatsapp'       => 'required|string',
            'alamat'         => 'required|string',
            'purpose'        => 'required|string',
            'payment_method' => 'required|in:cash,transfer,card',
            'foto_ktp'       => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Cek gear availability
        $gear = Gear::find($request->gear_id);
        if (!$gear || $gear->status !== 'available' || $gear->condition_status !== 'baik') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gear tidak tersedia untuk disewa'
            ], 400);
        }

        // Cek overlapping bookings
        $startReq = Carbon::parse($request->start_date);
        $endReq = $startReq->copy()->addDays((int) $request->duration);

        $isConflict = Rental::where('gear_id', $request->gear_id)
            ->whereIn('status', ['booking', 'active'])
            ->where(function ($query) use ($startReq, $endReq) {
                $query->where('start_date', '<', $endReq)
                      ->where('end_date', '>', $startReq);
            })->exists();

        if ($isConflict) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Jadwal alat sudah terisi pada tanggal tersebut'
            ], 400);
        }

        // Upload KTP
        if ($request->hasFile('foto_ktp')) {
            $fotoPath = $request->file('foto_ktp')->store('ktp', 'local');
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Foto KTP harus diupload'
            ], 422);
        }

        // Create rental
        $rental = Rental::create([
            'user_id'        => auth()->id(),
            'gear_id'        => $request->gear_id,
            'whatsapp'       => $request->whatsapp,
            'alamat'         => $request->alamat,
            'start_time'     => $request->start_time,
            'purpose'        => $request->purpose,
            'payment_method' => $request->payment_method,
            'start_date'     => $request->start_date,
            'end_date'       => $endReq,
            'duration'       => (int) $request->duration,
            'total_price'    => (int) $gear->rent_price * (int) $request->duration,
            'status'         => 'booking',
            'foto_ktp'       => $fotoPath,
            'note'           => 'Pendaftaran via API',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil dibuat dengan status booking',
            'data'    => $rental->load(['gear', 'user'])
        ], 201);
    }

    /**
     * PUT /api/rentals/{id}
     * Update rental
     */
    public function update(Request $request, $id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        // Hanya allow update untuk rental yang masih booking
        if ($rental->status !== 'booking') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya rental dengan status booking yang dapat diupdate'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'start_date'     => 'sometimes|date|after_or_equal:today',
            'duration'       => 'sometimes|integer|min:1',
            'whatsapp'       => 'sometimes|string',
            'alamat'         => 'sometimes|string',
            'purpose'        => 'sometimes|string',
            'payment_method' => 'sometimes|in:cash,transfer,card',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $rental->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil diupdate',
            'data'    => $rental
        ], 200);
    }

    /**
     * PATCH /api/rentals/{id}/confirm-payment
     * Konfirmasi pembayaran (admin mengaktifkan rental)
     */
    public function confirmPayment($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        if ($rental->status !== 'booking') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Status rental tidak valid untuk diaktifkan'
            ], 400);
        }

        $rental->update([
            'status' => 'active',
            'note'   => $rental->note . ' | Aktif oleh: ' . (auth()->user()->name ?? 'API') . ' pada ' . now()
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil diaktifkan',
            'data'    => $rental->load(['gear', 'user'])
        ], 200);
    }

    /**
     * PATCH /api/rentals/{id}/complete
     * Selesaikan rental dan hitung denda
     */
    public function complete($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        if ($rental->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya rental dengan status active yang dapat diselesaikan'
            ], 400);
        }

        $penalty = $rental->penalty_details;

        $rental->update([
            'status'          => 'completed',
            'returned_at'     => now(),
            'penalty_amount'  => $penalty['total'],
            'total_days_late' => $penalty['days'],
            'final_amount'    => $rental->total_price + $penalty['total'],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil diselesaikan',
            'data'    => $rental->load(['gear', 'user']),
            'penalty_info' => [
                'is_late'      => $penalty['is_late'],
                'days_late'    => $penalty['days'],
                'penalty_fee'  => $penalty['total'],
                'final_amount' => $rental->total_price + $penalty['total']
            ]
        ], 200);
    }

    /**
     * PATCH /api/rentals/{id}/cancel
     * Batalkan rental
     */
    public function cancel($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        // Hanya allow cancel untuk booking atau active
        if (!in_array($rental->status, ['booking', 'active'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak dapat dibatalkan dari status ' . $rental->status
            ], 400);
        }

        $rental->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'note'         => $rental->note . ' | Dibatalkan'
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil dibatalkan',
            'data'    => $rental
        ], 200);
    }

    /**
     * DELETE /api/rentals/{id}
     * Hapus rental (hard delete)
     */
    public function destroy($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        $rental->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental berhasil dihapus'
        ], 200);
    }

    /**
     * GET /api/rentals/user/{userId}
     * Mendapatkan rental user spesifik
     */
    public function userRentals($userId)
    {
        $rentals = Rental::where('user_id', $userId)
            ->with(['gear', 'gear.category'])
            ->latest()
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental user berhasil diambil',
            'data'    => $rentals
        ], 200);
    }

    /**
     * GET /api/rentals/gear/{gearId}
     * Mendapatkan rental schedule gear spesifik
     */
    public function gearRentals($gearId)
    {
        $rentals = Rental::where('gear_id', $gearId)
            ->whereIn('status', ['booking', 'active'])
            ->with(['user'])
            ->latest()
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Rental gear berhasil diambil',
            'data'    => $rentals
        ], 200);
    }

    /**
     * GET /api/rentals/{id}/ktp
     * Download foto KTP rental
     */
    public function downloadKtp($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental tidak ditemukan'
            ], 404);
        }

        if (!Storage::disk('local')->exists($rental->foto_ktp)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'File KTP tidak ditemukan'
            ], 404);
        }

        $path = Storage::disk('local')->path($rental->foto_ktp);
        return response()->file($path);
    }

    /**
     * GET /api/rentals/stats/dashboard
     * Mendapatkan statistik dashboard
     */
    public function dashboardStats()
    {
        $stats = [
            'booking_today'      => Rental::where('status', 'booking')->whereDate('start_date', today())->count(),
            'return_today'       => Rental::where('status', 'active')->whereDate('end_date', today())->count(),
            'overdue'            => Rental::where('status', 'active')->whereDate('end_date', '<', today())->count(),
            'total_booking'      => Rental::where('status', 'booking')->count(),
            'total_active'       => Rental::where('status', 'active')->count(),
            'total_completed'    => Rental::where('status', 'completed')->count(),
            'total_cancelled'    => Rental::where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Statistik dashboard berhasil diambil',
            'data'    => $stats
        ], 200);
    }
}
