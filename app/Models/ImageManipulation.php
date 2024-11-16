<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageManipulation extends Model
{
    const TYPE_RESIZE = 'resize';

    const UPDATED_AT = null;

    protected $fillable = ['name', 'path', 'type', 'data', 'output_path', 'user_id', 'album_id'];
}
