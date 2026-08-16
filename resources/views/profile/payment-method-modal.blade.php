<!-- Payment Method Modal -->
<div id="payment-method-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-lg max-w-md w-full border border-gray-200">
        <div class="px-6 py-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-green-50">
            <h3 class="text-lg font-bold text-emerald-700">Add Payment Method</h3>
        </div>

        <form id="payment-method-form" method="POST" action="{{ route('payment-methods.store') }}" class="p-6 space-y-4">
            @csrf

            <!-- Payment Type -->
            <div>
                <label for="type" class="block text-sm font-semibold text-emerald-700 mb-2">
                    <i class="fas fa-wallet text-emerald-600 mr-2"></i>Payment Type
                </label>
                <select id="type" name="type" required class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-700 font-medium transition">
                    <option value="">Select payment type</option>
                    <option value="card">💳 Credit/Debit Card</option>
                    <option value="bank">🏦 Bank Account</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Card Type (visible only when payment type is card) -->
            <div id="card-type-section" class="hidden">
                <label for="card_type" class="block text-sm font-semibold text-blue-700 mb-2">
                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>Payment Provider
                </label>
                <select id="card_type" name="card_type" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 font-medium transition">
                    <option value="">Select payment provider</option>
                    <option value="gcash">GCash</option>
                    <option value="maya">Maya</option>
                    <option value="bdo">BDO</option>
                </select>
                <div class="mt-3 flex items-center gap-3 text-xs text-gray-600">
                    <img src="https://www.thefastmode.com/media/k2/items/src/03160998318f697230a7e611fb0fa87d.jpg?t=20200629_013741" alt="GCash" class="h-6 w-10 object-contain rounded bg-white p-0.5 shadow-sm">
                    <img src="https://i.pinimg.com/originals/17/a6/de/17a6de136da9aa796bca4bf04315a0a1.png" alt="BDO" class="h-6 w-10 object-contain rounded bg-white p-0.5 shadow-sm">
                    <img src="https://cdn.manilastandard.net/wp-content/uploads/2025/02/maya-logo-black.png" alt="Maya" class="h-6 w-10 object-contain rounded bg-white p-0.5 shadow-sm">
                </div>
                @error('card_type')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Cardholder Name (visible only when payment type is card) -->
            <div id="cardholder-name-section" class="hidden">
                <label for="card_holder_name" class="block text-sm font-semibold text-blue-700 mb-2">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Cardholder Name
                </label>
                <input type="text" id="card_holder_name" name="card_holder_name" placeholder="Your full name" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 font-medium transition placeholder-gray-400">
                @error('card_holder_name')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
            <div id="card-number-section" class="hidden">
                <label for="card_number" class="block text-sm font-semibold text-blue-700 mb-2">
                    <i class="fas fa-hashtag text-blue-600 mr-2"></i>Account/Reference Number
                </label>
                <input type="text" id="card_number" name="card_number" placeholder="Your account or reference number" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 font-medium transition placeholder-gray-400">
                @error('card_number')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Expiry Month (visible only when payment type is card) -->
            <div id="expiry-month-section" class="hidden">
                <label for="expiry_month" class="block text-sm font-semibold text-purple-700 mb-2">
                    <i class="fas fa-calendar text-purple-600 mr-2"></i>Expiry Month
                </label>
                <select id="expiry_month" name="expiry_month" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-700 font-medium transition">
                    <option value="">MM</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
                @error('expiry_month')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Expiry Year (visible only when payment type is card) -->
            <div id="expiry-year-section" class="hidden">
                <label for="expiry_year" class="block text-sm font-semibold text-purple-700 mb-2">
                    <i class="fas fa-calendar text-purple-600 mr-2"></i>Expiry Year
                </label>
                <select id="expiry_year" name="expiry_year" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-700 font-medium transition">
                    <option value="">YYYY</option>
                    @for($y = now()->year; $y <= now()->year + 20; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                @error('expiry_year')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank Name (visible only when payment type is bank) -->
            <div id="bank-name-section" class="hidden">
                <label for="bank_name" class="block text-sm font-semibold text-indigo-700 mb-2">
                    <i class="fas fa-building text-indigo-600 mr-2"></i>Bank Name
                </label>
                <input type="text" id="bank_name" name="bank_name" placeholder="e.g., BDO, BPI, Metrobank" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 font-medium transition placeholder-gray-400">
                @error('bank_name')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Name (visible only when payment type is bank) -->
            <div id="account-name-section" class="hidden">
                <label for="account_name" class="block text-sm font-semibold text-indigo-700 mb-2">
                    <i class="fas fa-user text-indigo-600 mr-2"></i>Account Holder Name
                </label>
                <input type="text" id="account_name" name="account_name" placeholder="Full name" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 font-medium transition placeholder-gray-400">
                @error('account_name')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Number (visible only when payment type is bank) -->
            <div id="account-number-section" class="hidden">
                <label for="account_number" class="block text-sm font-semibold text-indigo-700 mb-2">
                    <i class="fas fa-key text-indigo-600 mr-2"></i>Account Number
                </label>
                <input type="text" id="account_number" name="account_number" placeholder="Bank account number" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 font-medium transition placeholder-gray-400">
                @error('account_number')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Set as Default -->
            <div class="flex items-center bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                <input type="checkbox" id="is_default" name="is_default" value="1" class="w-4 h-4 text-emerald-600 border-emerald-300 rounded focus:ring-emerald-500 cursor-pointer">
                <label for="is_default" class="ml-3 text-sm font-semibold text-emerald-700 cursor-pointer flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Set as default payment method
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closePaymentMethodModal()" class="flex-1 px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition active:scale-95">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-green-600 rounded-lg hover:from-emerald-700 hover:to-green-700 transition active:scale-95 shadow-md">
                    <i class="fas fa-check mr-2"></i>Save Payment Method
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddPaymentMethodModal() {
    document.getElementById('payment-method-modal').classList.remove('hidden');
    document.getElementById('payment-method-form').reset();
    updatePaymentTypeFields('');
}

function closePaymentMethodModal() {
    document.getElementById('payment-method-modal').classList.add('hidden');
}

function updatePaymentTypeFields(type) {
    // Hide all sections first
    document.getElementById('card-type-section').classList.add('hidden');
    document.getElementById('cardholder-name-section').classList.add('hidden');
    document.getElementById('card-number-section').classList.add('hidden');
    document.getElementById('expiry-month-section').classList.add('hidden');
    document.getElementById('expiry-year-section').classList.add('hidden');
    document.getElementById('bank-name-section').classList.add('hidden');
    document.getElementById('account-name-section').classList.add('hidden');
    document.getElementById('account-number-section').classList.add('hidden');

    // Show relevant sections based on type
    if (type === 'card') {
        document.getElementById('card-type-section').classList.remove('hidden');
        document.getElementById('cardholder-name-section').classList.remove('hidden');
        document.getElementById('card-number-section').classList.remove('hidden');
        document.getElementById('expiry-month-section').classList.remove('hidden');
        document.getElementById('expiry-year-section').classList.remove('hidden');
    } else if (type === 'bank') {
        document.getElementById('bank-name-section').classList.remove('hidden');
        document.getElementById('account-name-section').classList.remove('hidden');
        document.getElementById('account-number-section').classList.remove('hidden');
    }
}

// Listen to payment type change
document.getElementById('type')?.addEventListener('change', function() {
    updatePaymentTypeFields(this.value);
});

// Close modal when clicking outside
document.getElementById('payment-method-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentMethodModal();
    }
});

// Handle form submission
document.getElementById('payment-method-form')?.addEventListener('submit', function(e) {
    const type = document.getElementById('type').value;

    if (type === 'card') {
        // Validate card fields
        if (!document.getElementById('card_type').value ||
            !document.getElementById('card_holder_name').value ||
            !document.getElementById('card_number').value ||
            !document.getElementById('expiry_month').value ||
            !document.getElementById('expiry_year').value) {
            e.preventDefault();
            alert('Please fill in all card fields');
            return false;
        }
    } else if (type === 'bank') {
        // Validate bank fields
        if (!document.getElementById('bank_name').value ||
            !document.getElementById('account_name').value ||
            !document.getElementById('account_number').value) {
            e.preventDefault();
            alert('Please fill in all bank fields');
            return false;
        }
    } else {
        e.preventDefault();
        alert('Please select a payment type');
        return false;
    }
});

// Delete payment method
function deletePaymentMethod(paymentMethodId) {
    if (confirm('Are you sure you want to delete this payment method?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payment-methods/${paymentMethodId}`;
        form.innerHTML = `
            @csrf
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Set as default
function setAsDefault(paymentMethodId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/payment-methods/${paymentMethodId}/set-default`;
    form.innerHTML = `
        @csrf
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
