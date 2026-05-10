<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use Illuminate\Http\JsonResponse;

class PenaltyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Penalty::with('rental.client')->latest()->get());
    }

    public function show(Penalty $penalty): JsonResponse
    {
        return response()->json($penalty->load('rental.client', 'rental.items.gear'));
    }

    public function destroy(Penalty $penalty): JsonResponse
    {
        $penalty->delete();

        return response()->json([
            'message' => 'Denda berhasil dihapus.',
        ]);
    }
}
