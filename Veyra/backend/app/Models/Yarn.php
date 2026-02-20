<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yarn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'producing_organization',
        'producing_country_id',
        'address',
        'postal_code',
        'yarn_type',
        'percentage',
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

    protected $casts = [
        'percentage' => 'decimal:2',
        'renewable_energy_percentage' => 'decimal:2',
        'recycled_water_percentage' => 'decimal:2',
        'production_date' => 'date',
        'validity_date' => 'date',
        'has_certification' => 'boolean',
        'has_client_audit' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function producingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'producing_country_id');
    }
}
