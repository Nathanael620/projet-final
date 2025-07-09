<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Session;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display the payment form for a session.
     */
    public function create(Session $session): View
    {
        $this->authorize('pay', $session);

        $user = auth()->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'EUR',
            ]);
        }

        return view('payments.create', compact('session', 'wallet'));
    }

    /**
     * Process the payment.
     */
    public function process(Request $request, Session $session): RedirectResponse
    {
        $this->authorize('pay', $session);

        $request->validate([
            'payment_method' => 'required|in:wallet,stripe,paypal',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();
        $amount = $request->amount;

        try {
            DB::beginTransaction();

            // Créer le paiement
            $payment = Payment::create([
                'user_id' => $user->id,
                'session_id' => $session->id,
                'payment_id' => 'PAY_' . Str::random(16),
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'amount' => $amount,
                'currency' => 'EUR',
                'description' => "Paiement pour la session: {$session->title}",
            ]);

            // Traiter le paiement selon la méthode
            if ($request->payment_method === 'wallet') {
                $result = $this->processWalletPayment($user, $amount, $payment);
            } else {
                $result = $this->processExternalPayment($request->payment_method, $amount, $payment);
            }

            if ($result) {
                DB::commit();
                return redirect()->route('payments.success', $payment)
                    ->with('success', 'Paiement effectué avec succès !');
            } else {
                DB::rollBack();
                return back()->withErrors(['payment' => 'Le paiement a échoué. Veuillez réessayer.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['payment' => 'Une erreur est survenue lors du paiement.']);
        }
    }

    /**
     * Process wallet payment.
     */
    private function processWalletPayment($user, float $amount, Payment $payment): bool
    {
        $wallet = $user->wallet;

        if (!$wallet || !$wallet->hasSufficientBalance($amount)) {
            return false;
        }

        // Effectuer le paiement depuis le portefeuille
        $transaction = $wallet->makePayment($amount, "Paiement session #{$payment->session_id}", [
            'payment_id' => $payment->payment_id,
            'session_id' => $payment->session_id,
        ]);

        if ($transaction) {
            // Marquer le paiement comme complété
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Process external payment (Stripe, PayPal).
     */
    private function processExternalPayment(string $method, float $amount, Payment $payment): bool
    {
        // Simulation de paiement externe
        // En production, intégrer avec Stripe ou PayPal
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return true;
    }

    /**
     * Display payment success page.
     */
    public function success(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('payments.success', compact('payment'));
    }

    /**
     * Display payment history.
     */
    public function history(): View
    {
        $user = auth()->user();
        $payments = $user->payments()->with('session')->latest()->paginate(10);

        return view('payments.history', compact('payments'));
    }

    /**
     * Display wallet page.
     */
    public function wallet(): View
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'EUR',
            ]);
        }

        $transactions = $wallet->transactions()->latest()->paginate(10);

        return view('payments.wallet', compact('wallet', 'transactions'));
    }

    /**
     * Add funds to wallet.
     */
    public function addFunds(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000',
            'payment_method' => 'required|in:stripe,paypal',
        ]);

        $user = auth()->user();
        $wallet = $user->wallet;
        $amount = $request->amount;

        try {
            DB::beginTransaction();

            // Simuler l'ajout de fonds
            $transaction = $wallet->addFunds($amount, "Rechargement portefeuille", [
                'payment_method' => $request->payment_method,
            ]);

            DB::commit();

            return back()->with('success', "{$amount}€ ajoutés à votre portefeuille avec succès !");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['wallet' => 'Erreur lors de l\'ajout de fonds.']);
        }
    }

    /**
     * Withdraw funds from wallet.
     */
    public function withdrawFunds(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        $wallet = $user->wallet;
        $amount = $request->amount;

        if (!$wallet->hasSufficientBalance($amount)) {
            return back()->withErrors(['wallet' => 'Solde insuffisant.']);
        }

        try {
            DB::beginTransaction();

            $transaction = $wallet->withdrawFunds($amount, "Retrait portefeuille");

            DB::commit();

            return back()->with('success', "Retrait de {$amount}€ effectué avec succès !");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['wallet' => 'Erreur lors du retrait.']);
        }
    }
} 