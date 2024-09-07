<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id run uuid
 * @property $code run code
 * @property $name run name
 */
class Run extends Model
{
    use HasFactory, HasUuids, HasTimestamps;

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }

}
