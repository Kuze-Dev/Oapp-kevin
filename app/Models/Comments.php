<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\Replies;
use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comments extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'body',
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Replies::class, 'comment_id'); // Make sure the correct foreign key is used
    }

    // In App\Models\User.php

    public function likes(): HasMany
{
    return $this->hasMany(CommentLike::class,'comment_id');
}

public function likedByUser()
{
    return $this->hasOne(CommentLike::class)->where('user_id', auth()->id());
}




}
