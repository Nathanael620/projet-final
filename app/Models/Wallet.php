<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'is_active',
        'last_transaction_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'last_transaction_at' => 'datetime',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Add funds to wallet.
     */
    public function addFunds(float $amount, string $description = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->last_transaction_at = now();
        $this->save();

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transaction_id' => 'TXN_' . uniqid(),
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Withdraw funds from wallet.
     */
    public function withdrawFunds(float $amount, string $description = null, array $metadata = []): ?WalletTransaction
    {
        if (!$this->hasSufficientBalance($amount)) {
            return null;
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->last_transaction_at = now();
        $this->save();

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transaction_id' => 'TXN_' . uniqid(),
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Make payment from wallet.
     */
    public function makePayment(float $amount, string $description = null, array $metadata = []): ?WalletTransaction
    {
        if (!$this->hasSufficientBalance($amount)) {
            return null;
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->last_transaction_at = now();
        $this->save();

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transaction_id' => 'TXN_' . uniqid(),
            'type' => 'payment',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get formatted balance.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2) . ' €';
    }
} 