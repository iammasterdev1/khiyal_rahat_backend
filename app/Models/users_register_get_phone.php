<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed phone_number
 * @property int|mixed verification_code
 * @property mixed send_request_ip
 * @property mixed|string token
 * @method static where(string $string, string $string1, $get)
 */
class users_register_get_phone extends Model
{
protected $table = 'users_register_get_phones';
    use HasFactory;
}
