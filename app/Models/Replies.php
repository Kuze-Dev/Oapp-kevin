<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Replies extends Model
{

    protected $fillable = [
        'comment_id',
        'user_id',
        'body',
        'like',
    ];




    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comments::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
