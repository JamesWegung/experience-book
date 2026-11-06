<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $content_type_id
 * @property string $name
 * @property string $slug
 */
class ContentAttribute extends Model
{
    public $fillable = [
        'content_type_id',
        'name',
        'slug',
    ];

}
