<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentAttributeValue extends Model
{
    public $fillable = [
        'content_id',
        'content_attribute_id',
        'value',
    ];

    public function contentAttribute(): BelongsTo
    {
        return $this->belongsTo(ContentAttribute::class);
    }
}
