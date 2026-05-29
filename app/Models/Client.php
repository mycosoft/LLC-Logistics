<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
    ];

    /**
     * Auto-format phone number to 256 international format.
     */
    public function setPhoneAttribute($value)
    {
        if (!empty($value)) {
            $value = preg_replace('/[^0-9+]/', '', $value);
            $value = ltrim($value, '+');
            if (str_starts_with($value, '0')) {
                $value = '256' . substr($value, 1);
            } elseif (!str_starts_with($value, '256')) {
                $value = '256' . $value;
            }
        }
        $this->attributes['phone'] = $value;
    }

    /**
     * Get the shipments for the client.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get all invoices for the client through shipments.
     */
    public function invoices()
    {
        return $this->hasManyThrough(
            \App\Models\Invoice::class,
            Shipment::class
        );
    }
}
