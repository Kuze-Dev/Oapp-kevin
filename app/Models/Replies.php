<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comments;
use App\Models\ReplyLike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Replies extends Model
{

    protected $fillable = [
        'comment_id',
        'user_id',
        'body',
    ];




    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comments::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function likes():HasMany
{
    return $this->hasMany(ReplyLike::class);
}

public function likedByUser():HasOne
{
    return $this->hasOne(ReplyLike::class)->where('user_id', auth()->id());
}


}
