<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property Uuid $id
 * @property string $code
 * @property string $name
 * @property string $address
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $comment
 */
class Warehouse extends Model
{
    use HasFactory, HasUuids, Timestamp;
}
