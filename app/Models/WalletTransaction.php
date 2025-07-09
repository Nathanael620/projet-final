<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'currency',
        'description',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if transaction is a deposit.
     */
    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    /**
     * Check if transaction is a withdrawal.
     */
    public function isWithdrawal(): bool
    {
        return $this->type === 'withdrawal';
    }

    /**
     * Check if transaction is a payment.
     */
    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    /**
     * Check if transaction is a refund.
     */
    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }

    /**
     * Get formatted amount with sign.
     */
    public function getFormattedAmountWithSignAttribute(): string
    {
        $sign = $this->isDeposit() || $this->isRefund() ? '+' : '-';
        return $sign . number_format($this->amount, 2) . ' €';
    }

    /**
     * Get type badge class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'bg-green-100 text-green-800',
            'withdrawal' => 'bg-red-100 text-red-800',
            'payment' => 'bg-blue-100 text-blue-800',
            'refund' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'Dépôt',
            'withdrawal' => 'Retrait',
            'payment' => 'Paiement',
            'refund' => 'Remboursement',
            default => 'Transaction',
        };
    }
} 