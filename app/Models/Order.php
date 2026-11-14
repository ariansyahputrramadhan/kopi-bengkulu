<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'recipient_name',
        'phone',
        'address',
        'city',
        'postal_code',
        'total_amount',
        'payment_status',
        'payment_type',
        'snap_token',
        'paid_at',
        'order_status',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    // URL memakai nomor invoice (bukan ID berurutan yang mudah ditebak)
    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'paid'    => 'Sudah Dibayar',
            'pending' => 'Menunggu Pembayaran',
            'expired' => 'Kedaluwarsa',
            'failed'  => 'Pembayaran Gagal',
            default   => ucfirst($this->payment_status),
        };
    }

    public function paymentBadgeClass(): string
    {
        return match ($this->payment_status) {
            'paid'    => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            default   => 'bg-red-100 text-red-800',
        };
    }

    public function orderStatusLabel(): string
    {
        return match ($this->order_status) {
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            default      => ucfirst($this->order_status),
        };
    }

    public function orderBadgeClass(): string
    {
        return match ($this->order_status) {
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped'    => 'bg-indigo-100 text-indigo-800',
            'completed'  => 'bg-green-100 text-green-800',
            'cancelled'  => 'bg-red-100 text-red-800',
            default      => 'bg-gray-100 text-gray-700',
        };
    }
}