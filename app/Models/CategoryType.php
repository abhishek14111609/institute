<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    use HasFactory;

    public const MODULE_EXPENSE = 'expense';
    public const MODULE_INVENTORY = 'inventory';

    protected $fillable = [
        'school_id',
        'module',
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeForSchoolModule($query, int $schoolId, string $module)
    {
        return $query->where('school_id', $schoolId)->where('module', $module);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
