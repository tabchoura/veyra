<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRepair extends Model
{
    protected $fillable = [
        'product_usage_id',
        'repair_date',
        'repair_action_id',
        'other_text',
        'country_id',
        'organization',
    ];

    public function usage()
    {
        return $this->belongsTo(ProductUsage::class, 'product_usage_id');
    }

    public function action()
    {
        return $this->belongsTo(RepairAction::class, 'repair_action_id');
    }
}
