<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function content(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

}
