<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlueprintStatistic extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'blueprint_id',
        'views_count',
        'runs_count',
        'last_viewed_at',
        'last_run_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'views_count' => 'integer',
        'runs_count' => 'integer',
        'last_viewed_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get the blueprint that owns the statistics.
     */
    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }
} 