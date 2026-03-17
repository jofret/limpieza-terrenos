<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'post_id',
        'gender',
        'occupation',
        'birthday_month',
        'birthday_day',
        'comment',
        'is_published',
        'token',
        'sent_at',
        'answered_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sent_at' => 'datetime',
        'answered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}