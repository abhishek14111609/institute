<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $table = 'inventory_items';

    protected $fillable = [
        'school_id',
        'course_id',
        'level_id',
        'name',
        'category',
        'price',
        'stock_quantity',
        'alert_quantity',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function sales()
    {
        return $this->hasMany(InventorySale::class, 'item_id');
    }
}
