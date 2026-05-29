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
        'charge_type',
        'current_status',
        'ready_for_pickup_at',
        'auction_notified_at',
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
        'ready_for_pickup_at' => 'datetime',
        'auction_notified_at' => 'datetime',
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
     * Get invoices for the shipment
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get status updates for the shipment
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
     * Generate a unique tracking number
     */
    public static function generateTrackingNumber()
    {
        do {
            $number = 'LLC-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('tracking_number', $number)->exists());
        return $number;
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
