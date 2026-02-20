<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishTreatment extends Model
{
    use HasFactory;

    protected $table = 'finish_treatments';

    protected $fillable = [
        'name',
    ];
}
