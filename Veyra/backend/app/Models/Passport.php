<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passport extends Model
{
    protected $fillable = [
        'product_id',
        'created_by',
        'with_qr',
        'status',

        'environmental_summary',
        'is_generated',
        'generated_at',
        'payload_snapshot',

        'access_level',
        'public_token',
        'partner_emails',
        'published_at',
          'completed_steps',
    'total_steps',
    ];

    protected $casts = [
        'with_qr' => 'boolean',
        'environmental_summary' => 'array',
        'payload_snapshot' => 'array',
        'is_generated' => 'boolean',
        'generated_at' => 'datetime',
        'partner_emails' => 'array',
        'published_at' => 'datetime',
          'completed_steps' => 'integer',
    'total_steps' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
