<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paiement réussi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Paiement effectué avec succès !</h3>
                        <p class="text-gray-600">Votre session a été confirmée et payée.</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h4 class="font-medium text-gray-900 mb-4">Détails du paiement</h4>
                        <div class="space-y-3 text-left">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID de paiement:</span>
                                <span class="font-mono text-sm">{{ $payment->payment_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant:</span>
                                <span class="font-semibold">{{ $payment->formatted_amount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Méthode:</span>
                                <span class="capitalize">{{ $payment->payment_method }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span>{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($payment->session)
                    <div class="bg-blue-50 p-6 rounded-lg mb-6">
                        <h4 class="font-medium text-gray-900 mb-4">Détails de la session</h4>
                        <div class="space-y-3 text-left">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Titre:</span>
                                <span class="font-semibold">{{ $payment->session->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tuteur:</span>
                                <span>{{ $payment->session->tutor->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span>{{ $payment->session->date->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Durée:</span>
                                <span>{{ $payment->session->duration }} minutes</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                            Retour au tableau de bord
                        </a>
                        <a href="{{ route('payments.history') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                            Voir l'historique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 