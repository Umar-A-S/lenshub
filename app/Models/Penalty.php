<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'late_duration_minutes',
        'penalty_amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'late_duration_minutes' => 'integer',
            'penalty_amount' => 'decimal:2',
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}
