<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassportAccessEmail extends Model
{
    protected $fillable = [
        'passport_id',
        'email',
    ];

    public function passport()
    {
        return $this->belongsTo(Passport::class);
    }
}
