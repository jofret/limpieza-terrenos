<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'zone', 'birthday',
        'customer_type', 'status', 'preferred_contact', 'notes', 'metadata'
    ];

    protected $casts = [
        'birthday' => 'date',
        'metadata' => 'array',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}