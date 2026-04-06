<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function rules()
    {
        return [
            'content_type_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'rating' => 'numeric|max:10',
            'review' => 'string|nullable',
            'completed_date' => 'date',
            'completed_time' => 'date_format:H:i',
        ];
    }
}
