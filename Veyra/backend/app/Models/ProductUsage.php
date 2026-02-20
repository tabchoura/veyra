<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUsage extends Model
{
    protected $fillable = [
        'product_id',
        'brand',
        'delivery_country_id',
        'delivery_date',
        'washing_temperature',
        'hand_wash',
        'machine_wash',
        'dry_clean',
        'bleach',
        'dry_shade',
        'tumble_dry',
        'ironing',
        'is_repairable',
        'repair_comment',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function repairs()
    {
        return $this->hasMany(ProductRepair::class, 'product_usage_id');
    }
}
