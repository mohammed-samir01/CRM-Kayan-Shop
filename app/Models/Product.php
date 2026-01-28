<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public const AVAILABLE_SIZES = ['Small', 'Medium', 'Large', 'XL', '2XL', '3XL', 'Oversize', 'One Size'];
    public const AVAILABLE_COLORS = ['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Gray', 'Purple', 'Orange', 'Pink', 'Brown', 'Beige'];

    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock',
        'sizes',
        'colors',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sizes' => 'array',
        'colors' => 'array',
    ];
}
