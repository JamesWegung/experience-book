<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    public $fillable = [
        'content_id',
        'attribute_id',
        'value',
    ];
}
