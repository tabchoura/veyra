<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function passports()
    {
        return $this->belongsToMany(Passport::class, 'passport_partner')
            ->withTimestamps();
    }
}
