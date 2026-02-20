<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fabric extends Model
{
    protected $fillable = [
        'product_id',
        'producing_country_id',
        'producing_organization',
        'address',
        'postal_code',
        'fabric_type_id',
        'percentage',
        'production_date',
        'has_dyeing',
        'dyeing_type',
        'has_finishing',
        'finishing_type',
        'has_certification',
        'certificate_number',
        'validity_date',
        'transaction_reference',
        'has_client_audit',
        'audit_comments',
        'renewable_energy_percentage',
        'recycled_water_percentage',
        'zdhc_supply_to_zero',
        'zdhc_get_zd',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
