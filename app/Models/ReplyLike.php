<?php

namespace App\Models;

use App\Models\User;
use App\Models\Replies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReplyLike extends Model
{
    protected $fillable = [
        'user_id',
        'reply_id',
    ];
    //
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(Replies::class);
    }


}
