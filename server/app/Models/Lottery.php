<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lottery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'number',
        'date',
        'color',
        'next_super',
        'ball1',
        'ball2',
        'ball3',
        'ball4',
        'ball5',
        'ball6',
        'ball7',
        'special1',
        'special1_label',
        'special2',
        'special2_label',
        'special3',
        'special3_label',
        'special4',
        'special4_label',
        'total',
        'count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'next_date' => 'date',
        'next_super' => 'decimal:2',
        'total' => 'decimal:2',
        'count' => 'integer',
    ];
}
