<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'total_price',
        'status',
        'started_at',
        'end_at',
        'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'end_at' => 'datetime',
            'returned_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function penalty(): HasOne
    {
        return $this->hasOne(Penalty::class);
    }

    public function isLate(): bool
    {
        return $this->returned_at !== null && Carbon::parse($this->returned_at)->greaterThan(Carbon::parse($this->end_at));
    }
}
