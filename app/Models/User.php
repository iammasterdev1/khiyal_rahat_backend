<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed|string firstName
 * @property mixed|string lastName
 * @property mixed|string phone_number
 * @property mixed|string phone_number_verified_at
 * @property mixed|string token
 * @property false|mixed|string|null password
 * @property int|mixed account_type
 * @property mixed major
 * @property mixed study_area
 * @property mixed|string email
 * @property mixed ip_address
 * @method static where(string $string, string $string1, $get)
 * @method static find($id)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable , SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'email_verified_at' ,
        'phone_number_verified_at' ,
        'deleted_at' ,
        'ip_address'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function showCartItems(){
        return $this->hasMany(
            ecommerce_basket::class ,
        );
    }

    public function showAddresses(){
        return $this->hasMany(
            addresses::class
        );
    }

    public function lessonPurched()
    {
        return $this->hasMany(LessonPurchased::class);
    }

    public function coursePurched()
    {
        return $this->hasMany(purchases_courses::class);
    }


    public function basckets()
    {
        return $this->hasMany(school_basket::class);
    }

    public function showOrders (){

        return $this->hasMany(
            orders::class ,
            'user_id'
        )->orderByDesc('created_at');
    }

public function getFullName()
    {
        return "{$this->firstName} {$this->lastName}";
    }

}
