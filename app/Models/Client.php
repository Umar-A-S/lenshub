<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'name',
        'whatsapp_number',
        'address',
        'identity_photo',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
