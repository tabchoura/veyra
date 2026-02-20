<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Manufacturing extends Model
{
    protected $fillable = [
        'product_id',
        'producing_organization',
        'producing_country_id',
        'address',
        'postal_code',
        'production_date',
        'has_certification',
        'certificate_number',
        'validity_date',
        'transaction_reference',
        'has_client_audit',
        'audit_comments',
        'special_effects',

        // ✅ nouveaux champs
        'finishing_method_id',
        'colouring_method_id',
        'finish_treatment_id',

        'comments',
        'renewable_energy_percentage',
        'recycled_water_percentage',
        'zdhc_supply_to_zero',
        'zdhc_get_zd',
    ];

    public function finishingMethod()
    {
        return $this->belongsTo(\App\Models\FinishingMethod::class);
    }

    public function colouringMethod()
    {
        return $this->belongsTo(\App\Models\ColouringMethod::class);
    }

    public function finishTreatment()
    {
        return $this->belongsTo(\App\Models\FinishTreatment::class);
    }
}
