<?php

namespace App\Models;

use App\Filters\EventFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'content_id',
      'rating',
      'review',
      'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function scopeGetContentTypeStats(Builder $query, int $userId): Builder
    {
        return $query->where('events.user_id', $userId)
            ->join('contents', 'events.content_id', '=', 'contents.id')
            ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
            ->select(
                'content_types.slug',
                DB::raw('count(*) as amount')
            )
            ->groupBy('contents.content_type_id', 'content_types.slug');
    }

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return (new EventFilter($request))->apply($query);
    }


}
