<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadFile extends Model
{
    protected $fillable = [
        'batch_id',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'checksum',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    /**
     * Get the batch this file belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(UploadBatch::class, 'batch_id');
    }
}
