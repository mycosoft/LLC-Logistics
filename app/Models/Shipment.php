<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'receiver_id',
        'tracking_number',
        'origin',
        'destination',
        'weight',
        'shipment_type',
        'current_status',
        'description',
        'expected_delivery_date',
        // Delivery Range
        'delivery_time_min',
        'delivery_time_max',
        'delivery_time_unit',
        // Batch Information
        'batch_id',
        // Package Details
        'num_packages',
        'length',
        'width',
        'height',
        'cbm',
        'package_type',
        'fragile',
        'special_instructions',
        // Pricing & Billing
        'shipping_cost',
        'insurance_value',
        'tax',
        'discount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        // Sender Information
        'sender_name',
        'sender_phone',
        'sender_address',
        // Receiver Information
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        // Additional Details
        'service_type',
        'delivery_instructions',
        'reference_number',
        'special_notes',
        // Customs Information
        'is_international',
        'customs_value',
        'customs_description',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'fragile' => 'boolean',
        'is_international' => 'boolean',
        'shipping_cost' => 'decimal:2',
        'insurance_value' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'customs_value' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'cbm' => 'decimal:3',
    ];


    /**
     * Boot the model and generate tracking number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            if (empty($shipment->tracking_number)) {
                $shipment->tracking_number = self::generateTrackingNumber();
            }
        });
    }

    /**
     * Generate a unique tracking number
     * Format: BRY-YYYYMMDD-UNIQUENUMBER (e.g., BRY-20251129-000001)
     */
    public static function generateTrackingNumber()
    {
        $date = date('Ymd');
        $prefix = 'BRY-' . $date . '-';
        
        // Get the last shipment created today
        $lastShipment = self::where('tracking_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastShipment) {
            // Extract the unique ID from the last tracking number
            $lastNumber = (int) substr($lastShipment->tracking_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Pad with zeros to make it 6 digits
        $uniqueId = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return $prefix . $uniqueId;
    }

    /**
     * Get the client that owns the shipment
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the receiver client (if applicable)
     */
    public function receiver()
    {
        return $this->belongsTo(Client::class, 'receiver_id');
    }

    /**
     * Get the primary invoice for the shipment
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class)->latest();
    }

    /**
     * Get the invoices for the shipment
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the status updates for the shipment
     */
    public function statusUpdates()
    {
        return $this->hasMany(ShipmentStatusUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the batch that owns the shipment
     */
    public function batch()
    {
        return $this->belongsTo(ShipmentBatch::class, 'batch_id');
    }

    /**
     * Get formatted delivery range (e.g., "5-7 days" or "2-3 months")
     */
    public function getDeliveryRangeAttribute()
    {
        if ($this->delivery_time_min && $this->delivery_time_max && $this->delivery_time_unit) {
            return $this->delivery_time_min . '-' . $this->delivery_time_max . ' ' . $this->delivery_time_unit;
        }
        return null;
    }
}
