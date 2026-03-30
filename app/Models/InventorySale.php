<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorySale extends Model
{
    use HasFactory, MultiTenant;

    protected $table = 'inventory_sales';

    protected $fillable = [
        'school_id',
        'student_id',
        'item_id',
        'quantity',
        'unit_price',
        'total_amount',
        'payment_status',
        'invoice_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
