<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;

use App\Models\User;

class SocialProfile extends Model
{
    use HasFactory;
/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'social_id',
        'social_name',
        'social_avatar'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
