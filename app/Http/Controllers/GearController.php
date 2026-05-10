<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GearController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Gear::latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'stock_total' => ['required', 'integer', 'min:0'],
            'stock_available' => ['required', 'integer', 'min:0', 'lte:stock_total'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,maintenance'],
        ]);

        $gear = Gear::create($data);

        return response()->json([
            'message' => 'Gear berhasil ditambahkan.',
            'data' => $gear,
        ], 201);
    }

    public function show(Gear $gear): JsonResponse
    {
        return response()->json($gear->load('rentalItems.rental'));
    }

    public function update(Request $request, Gear $gear): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'stock_total' => ['required', 'integer', 'min:0'],
            'stock_available' => ['required', 'integer', 'min:0', 'lte:stock_total'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,maintenance'],
        ]);

        $gear->update($data);

        return response()->json([
            'message' => 'Gear berhasil diperbarui.',
            'data' => $gear,
        ]);
    }

    public function destroy(Gear $gear): JsonResponse
    {
        $gear->delete();

        return response()->json([
            'message' => 'Gear berhasil dihapus.',
        ]);
    }
}
