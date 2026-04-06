<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $fillable = [
        'content_type_id',
        'name',
        'slug',
    ];

}
