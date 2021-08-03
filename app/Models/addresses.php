<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string province
 * @property mixed|string city
 * @property mixed|string address
 * @property mixed|string postcode
 * @property int|mixed user_id
 * @method static find($get)
 */
class addresses extends Model
{
    use HasFactory;
}
