<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_code',
        'campaign_id',
        'assigned_to',
        'customer_name',
        'phone',
        'email',
        'city',
        'address',
        'status',
        'expected_value',
        'follow_up_date',
        'notes',
    ];

    protected $casts = [
        'expected_value' => 'decimal:2',
        'follow_up_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($lead) {
            $lead->lead_code = 'LEAD-' . str_pad($lead->id, 6, '0', STR_PAD_LEFT);
            $lead->saveQuietly();
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activities(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject')->latest();
    }
}
