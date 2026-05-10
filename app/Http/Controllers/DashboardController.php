<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Gear;
use App\Models\Penalty;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function owner(Request $request): View
    {
        return view('owner.dashboard', $this->buildDashboardData());
    }

    public function admin(Request $request): View
    {
        return view('admin.dashboard', $this->buildDashboardData());
    }

    public function redirect(Request $request)
    {
        return match ($request->user()?->role) {
            'owner' => redirect()->route('owner.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect('/'),
        };
    }

    private function buildDashboardData(): array
    {
        $summary = [
            'clients' => Client::count(),
            'gears' => Gear::count(),
            'active_rentals' => Rental::where('status', 'active')->count(),
            'late_rentals' => Rental::where('status', 'late')->count(),
            'total_penalty' => Penalty::sum('penalty_amount'),
            'total_revenue' => Rental::whereIn('status', ['completed', 'late'])->sum('total_price'),
        ];

        $recentRentals = Rental::with(['client', 'items.gear', 'penalty'])
            ->latest()
            ->limit(5)
            ->get();

        return compact('summary', 'recentRentals');
    }
}
