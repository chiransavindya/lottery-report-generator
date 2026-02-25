<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'draw_id',
        'generated_by',
        'status',
        'version',
        'pdf_path_en',
        'pdf_path_si',
        'pdf_path_ta',
        'published_at',
        'archived_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * Get the draw this report is for.
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /**
     * Get the user who generated the report.
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Alias for generatedBy() - for compatibility.
     */
    public function generator(): BelongsTo
    {
        return $this->generatedBy();
    }

    /**
     * Check if report is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if report is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if report is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
