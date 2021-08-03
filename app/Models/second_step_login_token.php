<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string token
 * @property int|mixed code
 * @method static where(string $string, string $string1, $get)
 */
class second_step_login_token extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */
    private $user_id;
}
