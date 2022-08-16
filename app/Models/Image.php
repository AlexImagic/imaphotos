<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'image_id',
        'image_path',
        'image_size',
        'method',
        'views',
    ];

    /**
     * Get the user for image.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
