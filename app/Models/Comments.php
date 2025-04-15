<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\Replies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comments extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'body',
        'like'
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




}
