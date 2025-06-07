<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** @typescript **/
class Blueprint extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'php_version',
        'wordpress_version',
        'steps',
        'user_id',
        'is_anonymous',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'steps' => 'array',
        'is_anonymous' => 'boolean',
    ];

    /**
     * Get the user that owns the blueprint.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the blueprint statistics.
     */
    public function statistics()
    {
        return $this->hasOne(BlueprintStatistic::class);
    }
} 