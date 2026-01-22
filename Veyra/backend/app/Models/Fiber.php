<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fiber extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'fiber_id',
        'percentage',
        'origin_country_id',
        'transaction_date',
        'has_certification',
        'certificate_number',
        'validity_date',
        'transaction_reference',
        'has_client_audit',
        'audit_comments',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'transaction_date' => 'date',
        'validity_date' => 'date',
        'has_certification' => 'boolean',
        'has_client_audit' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Important: on garde le nom "fiber" pour matcher ton controller/front existant
    public function fiber(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'fiber_id');
    }

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }
}
