<?php

namespace App\Http\Controllers\ContentType;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentType\ContentTypeAttributesResource;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetAttributesController extends Controller
{
    public function __invoke(Request $request, ContentType $contentType): AnonymousResourceCollection
    {
        $attributes = $contentType->contentAttributes;

        return ContentTypeAttributesResource::collection($attributes);
    }
}
