<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorPricing extends Model
{
    use HasFactory;

    protected $table = 'major_pricing';

    protected $fillable = [
        'major_name',
        'major_key',
        'hourly_rate',
        'currency',
        'is_active',
        'description'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active pricing
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find pricing by major key
     */
    public static function findByMajorKey($majorKey)
    {
        return static::active()->where('major_key', $majorKey)->first();
    }

    /**
     * Find pricing by major name (fuzzy matching)
     */
    public static function findByMajorName($majorName)
    {
        $majorKey = strtolower(trim($majorName));

        // Try exact match first
        $pricing = static::findByMajorKey($majorKey);
        if ($pricing) {
            return $pricing;
        }

        // Try partial matching
        return static::active()
            ->where(function($query) use ($majorKey, $majorName) {
                $query->where('major_name', 'like', "%{$majorName}%")
                      ->orWhere('major_key', 'like', "%{$majorKey}%");
            })
            ->first();
    }

    /**
     * Get all active major pricing
     */
    public static function getActivePricing()
    {
        return static::active()->orderBy('major_name')->get();
    }
}
