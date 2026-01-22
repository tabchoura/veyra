<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'region'];

    // ✅ un material est utilisé dans plusieurs lignes "fibers"
    public function fibers()
    {
        return $this->hasMany(Fiber::class, 'fiber_id');
    }
}
