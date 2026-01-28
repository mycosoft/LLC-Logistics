<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentStatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status',
        'location',
        'remarks',
    ];

    /**
     * Get the shipment that owns the status update.
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
