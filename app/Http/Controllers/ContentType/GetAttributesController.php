<?php

namespace App\Http\Controllers\ContentType;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentType\ContentTypeAttributesResource;
use App\Models\ContentType;
use Illuminate\Http\Request;

class GetAttributesController extends Controller
{
    public function __invoke(Request $request, ContentType $contentType)
    {
        $attributes = $contentType->attributes;

        return ContentTypeAttributesResource::collection($attributes);
    }
}
