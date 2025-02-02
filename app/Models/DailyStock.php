<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStock extends Model
{
    use HasFactory, HasUuids, HasTimestamps;

    public $casts = [
        'stock_date' => 'date:Y-m-d',
        'stock' => 'array'
    ];

    public $fillable = [
      'stock_date',
      'stock',
      'filename',
      'hash',
    ];
}
