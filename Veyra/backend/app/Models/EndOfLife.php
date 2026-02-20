<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndOfLife extends Model
{
    protected $fillable = [
        'product_id',
        'is_recoverable',
        'comment',
        'end_of_life_date',
        'end_of_life_country_id',
        'reuse',
        'recycling',
        'incineration',
        'composting',
        'landfill',

        'recycling_country_id',
        'recycling_method',
        'recycling_valued_product',
        'recycling_organization',

        'incineration_country_id',
        'incineration_method',
        'incineration_valued_product',
        'incineration_organization',

        'composting_country_id',
        'composting_method',
        'composting_valued_product',
        'composting_organization',

        'landfill_country_id',
        'landfill_method',
        'landfill_valued_product',
        'landfill_organization',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
