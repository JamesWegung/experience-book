<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'content_type_id',
        'is_common',
        'user_id'
    ];

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }



}
