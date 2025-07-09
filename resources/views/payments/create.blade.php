<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paiement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la session</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Titre</p>
                                    <p class="text-lg font-semibold">{{ $session->title }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tuteur</p>
                                    <p class="text-lg">{{ $session->tutor->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date</p>
                                    <p class="text-lg">{{ $session->date->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Durée</p>
                                    <p class="text-lg">{{ $session->duration }} minutes</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Prix</p>
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($session->price, 2) }}€</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('payments.process', $session) }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $session->price }}">

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Méthode de paiement</h3>
                            
                            <!-- Portefeuille -->
                            <div class="mb-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="wallet" class="mr-3" 
                                           {{ $wallet->balance >= $session->price ? '' : 'disabled' }}>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium">Portefeuille</p>
                                                <p class="text-sm text-gray-500">Solde: {{ $wallet->formatted_balance }}</p>
                                            </div>
                                            @if($wallet->balance >= $session->price)
                                                <span class="text-green-600 text-sm">✓ Solde suffisant</span>
                                            @else
                                                <span class="text-red-600 text-sm">✗ Solde insuffisant</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Carte bancaire -->
                            <div class="mb-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="stripe" class="mr-3">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium">Carte bancaire</p>
                                                <p class="text-sm text-gray-500">Paiement sécurisé via Stripe</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <svg class="w-8 h-5" viewBox="0 0 24 16">
                                                    <rect width="24" height="16" rx="2" fill="#6772E5"/>
                                                    <path d="M6 6h12v4H6z" fill="white"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- PayPal -->
                            <div class="mb-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="paypal" class="mr-3">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium">PayPal</p>
                                                <p class="text-sm text-gray-500">Paiement rapide et sécurisé</p>
                                            </div>
                                            <div class="text-blue-600 font-bold">PayPal</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-medium">Total à payer:</span>
                                <span class="text-2xl font-bold text-green-600">{{ number_format($session->price, 2) }}€</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('sessions.show', $session) }}" 
                               class="text-gray-600 hover:text-gray-900">
                                ← Retour à la session
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                Payer maintenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 