<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [

        'nama',
        'ktp',
        'phone',
        'alamat',
        'status'
    ];

    public function rentals()
    {
        return $this->hasMany(
            Rental::class
        );
    }
}