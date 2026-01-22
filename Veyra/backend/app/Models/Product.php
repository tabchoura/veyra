<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    // ✅ Champs remplissables (volet 1/2/3) - sans item_code et creation_datetime
    protected $fillable = [
        // VOLET 1
        'product_image',
        'product_name',
        'weight',
        'batch_serial',
        'prodcom_code',
        'declaring_organization',
        'organization_country_id',
        'organization_address',
        'postal_code',
        'item_description',
        'volet_1_status',
        'volet_1_completed',

        // VOLET 2
        'category_id',
        'subcategory_id',
        'volet_2_status',
        'volet_2_completed',

        // VOLET 3
        'volet_3_status',
        'volet_3_completed',

        'user_id',
    ];

    protected $casts = [
        'creation_datetime' => 'datetime',
        'volet_1_completed' => 'boolean',
        'volet_2_completed' => 'boolean',
        'volet_3_completed' => 'boolean',
        'weight' => 'decimal:3',
        'postal_code' => 'string', // ✅ IMPORTANT
    ];

    /**
     * ✅ Générer automatiquement item_code si tu veux côté serveur
     * (si tu le génères déjà ailleurs, tu peux supprimer ce booted())
     */
    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (empty($product->item_code)) {
                $product->item_code = 'PROD-' . Str::upper((string) Str::ulid());
            }
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'organization_country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function fibers(): HasMany
    {
        return $this->hasMany(Fiber::class);
    }
}
