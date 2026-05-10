<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'stock_total',
        'stock_available',
        'price_per_hour',
        'price_per_day',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'stock_total' => 'integer',
            'stock_available' => 'integer',
        ];
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }
}
