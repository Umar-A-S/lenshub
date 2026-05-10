<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Client::latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'max:16', 'unique:clients,nik'],
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'identity_photo' => ['nullable', 'string', 'max:255'],
        ]);

        $client = Client::create($data);

        return response()->json([
            'message' => 'Client berhasil ditambahkan.',
            'data' => $client,
        ], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($client->load('rentals'));
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'max:16', 'unique:clients,nik,' . $client->id],
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'identity_photo' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($data);

        return response()->json([
            'message' => 'Client berhasil diperbarui.',
            'data' => $client,
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json([
            'message' => 'Client berhasil dihapus.',
        ]);
    }
}
