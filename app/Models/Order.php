<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_ref',
        'user_id',
        'product_id',
        'product_name',
        'category',
        'sku',
        'target',
        'notes',
        'base_price',
        'unique_code',
        'total_amount',
        'qris_payload',
        'status',
        'payment_method',
        'serial_number',
        'paid_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'base_price' => 'integer',
        'unique_code' => 'integer',
        'total_amount' => 'integer',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship with User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helper for status label & badge class.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'success', 'completed' => ['label' => 'Selesai', 'class' => 'badge-success', 'bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'],
            'processing' => ['label' => 'Diproses', 'class' => 'badge-info', 'bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#BFDBFE'],
            'paid' => ['label' => 'Dibayar', 'class' => 'badge-info', 'bg' => '#EEF2FF', 'color' => '#4361EE', 'border' => '#C7D2FE'],
            'failed' => ['label' => 'Gagal', 'class' => 'badge-danger', 'bg' => '#FEF2F2', 'color' => '#B91C1C', 'border' => '#FECACA'],
            'expired' => ['label' => 'Kadaluarsa', 'class' => 'badge-danger', 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1'],
            default => ['label' => 'Menunggu Pembayaran', 'class' => 'badge-warning', 'bg' => '#FFFBEB', 'color' => '#B45309', 'border' => '#FDE68A'],
        };
    }
}
