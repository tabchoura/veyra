<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BawearAssessment extends Model
{
  protected $fillable = [
    'product_id','source_type','pdf_path',
    'extracted_payload','normalized_payload',
    'score_value','score_unit','status','errors','created_by',
  ];

  protected $casts = [
    'extracted_payload' => 'array',
    'normalized_payload' => 'array',
    'errors' => 'array',
  ];

  public function product() {
    return $this->belongsTo(Product::class);
  }
}
