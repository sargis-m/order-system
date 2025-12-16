<?php

namespace App\Models;

use App\Models\Constants\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'partner_id',
        'customer_id',
        'status',
        'title',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function canBeAccepted(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    public function canBeRejected(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    public function accept(): void
    {
        if (!$this->canBeAccepted()) {
            throw new \RuntimeException('Order cannot be accepted. Current status: ' . $this->status);
        }
        
        $this->update(['status' => OrderStatus::ACCEPTED]);
    }

    public function reject(): void
    {
        if (!$this->canBeRejected()) {
            throw new \RuntimeException('Order cannot be rejected. Current status: ' . $this->status);
        }
        
        $this->update(['status' => OrderStatus::REJECTED]);
    }
}
