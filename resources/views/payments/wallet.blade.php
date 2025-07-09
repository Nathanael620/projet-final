<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Portefeuille') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Solde du portefeuille -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Solde actuel</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $wallet->formatted_balance }}</p>
                        </div>
                        <div class="flex space-x-4">
                            <button onclick="openAddFundsModal()" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Ajouter des fonds
                            </button>
                            <button onclick="openWithdrawModal()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Retirer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des transactions</h3>
                    
                    @if($transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Montant
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Solde après
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $transaction->type_badge_class }}">
                                                    {{ $transaction->type_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <span class="{{ $transaction->isDeposit() || $transaction->isRefund() ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->formatted_amount_with_sign }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($transaction->balance_after, 2) }}€
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $transaction->description }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune transaction</h3>
                            <p class="mt-1 text-sm text-gray-500">Commencez par ajouter des fonds à votre portefeuille.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter des fonds -->
    <div id="addFundsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter des fonds</h3>
                <form method="POST" action="{{ route('payments.add-funds') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Montant (€)</label>
                        <input type="number" id="amount" name="amount" min="1" max="1000" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="stripe" class="mr-2" checked>
                                <span>Carte bancaire</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="paypal" class="mr-2">
                                <span>PayPal</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddFundsModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Retrait -->
    <div id="withdrawModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Retirer des fonds</h3>
                <form method="POST" action="{{ route('payments.withdraw-funds') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="withdraw_amount" class="block text-sm font-medium text-gray-700 mb-2">Montant (€)</label>
                        <input type="number" id="withdraw_amount" name="amount" min="1" max="{{ $wallet->balance }}" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Solde disponible: {{ $wallet->formatted_balance }}</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeWithdrawModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Retirer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddFundsModal() {
            document.getElementById('addFundsModal').classList.remove('hidden');
        }

        function closeAddFundsModal() {
            document.getElementById('addFundsModal').classList.add('hidden');
        }

        function openWithdrawModal() {
            document.getElementById('withdrawModal').classList.remove('hidden');
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('hidden');
        }

        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const addFundsModal = document.getElementById('addFundsModal');
            const withdrawModal = document.getElementById('withdrawModal');
            
            if (event.target === addFundsModal) {
                closeAddFundsModal();
            }
            if (event.target === withdrawModal) {
                closeWithdrawModal();
            }
        }
    </script>
</x-app-layout> 