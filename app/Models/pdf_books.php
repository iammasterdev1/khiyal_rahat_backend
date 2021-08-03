<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed|string title
 * @property mixed|string description
 * @property int|mixed irr_price
 * @property mixed|string path
 * @property mixed cat_id
 */
class pdf_books extends Model
{
    use HasFactory , SoftDeletes;

}
