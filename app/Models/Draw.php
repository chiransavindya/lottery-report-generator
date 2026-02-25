<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Draw extends Model
{
    protected $fillable = [
        'lottery_type_id',
        'draw_date',
        'draw_number',
        'numbers',
        'bonus_number',
        'prize_breakdown',
        'total_sales',
        'jackpot_amount',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'draw_date' => 'date',
            'draw_number' => 'integer',
            'numbers' => 'array',
            'prize_breakdown' => 'array',
            'total_sales' => 'decimal:2',
            'jackpot_amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    /**
     * Clean numbers array - remove leading '0' from letters (e.g., "0I" becomes "I")
     */
    protected function getNumbersAttribute($value): array
    {
        $numbers = json_decode($value, true) ?? [];

        return array_map(function ($num) {
            // If value starts with '0' and second character is a letter, remove the '0'
            if (is_string($num) && strlen($num) >= 2 && $num[0] === '0' && ctype_alpha($num[1])) {
                return substr($num, 1); // Remove leading '0'
            }
            return $num;
        }, $numbers);
    }

    /**
     * Get the lottery type for this draw.
     */
    public function lotteryType(): BelongsTo
    {
        return $this->belongsTo(LotteryType::class);
    }

    /**
     * Get the reports for this draw.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get color from metadata.
     */
    public function getColorAttribute()
    {
        // Check if 'color' is being accessed and it's not from the database
        if (!array_key_exists('color', $this->attributes)) {
            return $this->metadata['color'] ?? null;
        }
        return $this->attributes['color'] ?? null;
    }

    /**
     * Get English letters from metadata.
     */
    public function getEnglishLettersAttribute()
    {
        if (!array_key_exists('english_letters', $this->attributes)) {
            $letters = $this->metadata['english_letters'] ?? null;
            if (is_array($letters)) {
                return implode(',', $letters);
            }
            return $letters;
        }
        return $this->attributes['english_letters'] ?? null;
    }

    /**
     * Get super number from metadata.
     */
    public function getSuperNumberAttribute()
    {
        if (!array_key_exists('super_number', $this->attributes)) {
            return $this->metadata['super_number'] ?? null;
        }
        return $this->attributes['super_number'] ?? null;
    }

    /**
     * Get zodiac sign from metadata.
     */
    public function getZodiacSignAttribute()
    {
        if (!array_key_exists('zodiac_sign', $this->attributes)) {
            return $this->metadata['zodiac_sign'] ?? null;
        }
        return $this->attributes['zodiac_sign'] ?? null;
    }

    /**
     * Get next jackpot from metadata.
     */
    public function getNextJackpotAttribute()
    {
        if (!array_key_exists('next_jackpot', $this->attributes)) {
            return $this->metadata['next_jackpot'] ?? null;
        }
        return $this->attributes['next_jackpot'] ?? null;
    }

    /**
     * Get special number from metadata.
     */
    public function getSpecialNumberAttribute()
    {
        if (!array_key_exists('special_number', $this->attributes)) {
            return $this->metadata['special_number'] ?? null;
        }
        return $this->attributes['special_number'] ?? null;
    }

    /**
     * Get total winners from metadata.
     */
    public function getTotalWinnersAttribute()
    {
        if (!array_key_exists('total_winners', $this->attributes)) {
            return $this->metadata['total_winners'] ?? null;
        }
        return $this->attributes['total_winners'] ?? null;
    }

    /**
     * Get total prize value from metadata.
     */
    public function getTotalPrizeValueAttribute()
    {
        if (!array_key_exists('total_prize_value', $this->attributes)) {
            return $this->metadata['total_prize_value'] ?? null;
        }
        return $this->attributes['total_prize_value'] ?? null;
    }
}
