<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EventFilter
{
    public function __construct(protected Request $request) {}

    public function apply(Builder $builder): Builder
    {
        return $builder
            ->when($this->request->categories, fn($q) => $this->filterByCategories($q))
            ->when($this->request->search, fn($q) => $this->filterBySearch($q))
            ->tap(fn($q) => $this->applySorting($q));
    }

    protected function filterByCategories($q): Builder
    {
        $selectedCategories = is_array($this->request->categories)
            ? $this->request->categories
            : explode(',', $this->request->categories);

        return $q->whereHas('content.contentType', fn($query) => $query->whereIn('id', $selectedCategories));
    }

    protected function filterBySearch($q): Builder
    {
        $search = $this->request->search;
        return $q->where(function ($query) use ($search) {
            $query->whereHas('content', function ($sub) use ($search) {
                $sub->where('name', 'LIKE', "%$search%")->orWhere('description', 'LIKE', "%$search%");
            })->orWhere('review', 'LIKE', "%$search%");
        });
    }


    protected function applySorting($q): Builder
    {
        $sort = $this->request->input('sort', 'created_at');
        $order = $this->request->input('order', 'desc');

        return match ($sort) {
            'completed_at' => $q->orderByRaw($order === 'asc'
                ? 'CASE WHEN completed_at IS NULL THEN 1 ELSE 0 END'
                : 'CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END')
                ->orderBy('completed_at', $order),

            'content_type' => $q->join('contents', 'events.content_id', '=', 'contents.id')
                ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
                ->select('events.*')
                ->orderBy('content_types.name', $order),

            default => $q->orderBy($sort, $order),
        };
    }

}
