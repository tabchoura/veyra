<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
        'product_id',
        'producing_organization',
        'producing_country_id',
        'address',
        'postal_code',
        'accessory_type_id',
        'accessory_type_other',
        'weight',
        'production_date',
        'has_certification',
        'certificate_number',
        'validity_date',
        'transaction_reference',
        'has_client_audit',
        'audit_comments',
        'renewable_energy_percentage',
        'recycled_water_percentage',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
