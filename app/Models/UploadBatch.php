<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UploadBatch extends Model
{
    protected $fillable = [
        'batch_name',
        'user_id',
        'lottery_type_id',
        'draw_date',
        'status',
        'total_files',
        'processed_files',
        'failed_files',
        'is_complete',
        'missing_lotteries',
        'date_buckets',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'draw_date' => 'date',
            'total_files' => 'integer',
            'processed_files' => 'integer',
            'failed_files' => 'integer',
            'is_complete' => 'boolean',
            'missing_lotteries' => 'array',
            'date_buckets' => 'array',
        ];
    }

    /**
     * Get the user who uploaded the batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lottery type for this batch.
     */
    public function lotteryType(): BelongsTo
    {
        return $this->belongsTo(LotteryType::class);
    }

    /**
     * Get the files in the batch.
     */
    public function files(): HasMany
    {
        return $this->hasMany(UploadFile::class, 'batch_id');
    }
}
