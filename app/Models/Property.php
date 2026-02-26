<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'customer_id', 'name', 'address', 'zone', 'total_area',
        'property_type', 'has_garden', 'garden_areas', 'has_pool', 'pools',
        'has_trees', 'trees', 'has_plants', 'plants',
        'has_sport_areas', 'sport_areas', 'other_features'
    ];

    protected $casts = [
        'garden_areas' => 'array',
        'pools' => 'array',
        'trees' => 'array',
        'plants' => 'array',
        'sport_areas' => 'array',
        'other_features' => 'array',
        'has_garden' => 'boolean',
        'has_pool' => 'boolean',
        'has_trees' => 'boolean',
        'has_plants' => 'boolean',
        'has_sport_areas' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'property_post')
                    ->withPivot('relation_type', 'comment', 'rating', 'service_date')
                    ->withTimestamps();
    }
}