<div class="space-y-4 orders-content">
    @if(isset($groupedOrders))
        @foreach(['pending' => 'To Pay', 'packed' => 'To Ship', 'shipped' => 'To Receive', 'delivered' => 'Completed', 'cancelled' => 'Cancelled'] as $bucket => $title)
            <div class="mb-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $title }}</h4>
                @if(isset($groupedOrders[$bucket]) && $groupedOrders[$bucket]->count())
                    <div class="space-y-4">
                        @foreach($groupedOrders[$bucket] as $order)
                            <div class="border border-gray-100 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                            @if($order->items->first() && $order->items->first()->product && $order->items->first()->product->image_url)
                                                <img src="{{ $order->items->first()->product->image_url }}" alt="Product image for {{ $order->items->first()->product->name ?? 'order item' }}" class="object-cover w-full h-full" onerror="this.onerror=null;this.src='https://placehold.co/100x100/FF7F00/ffffff?text=No+Image';">
                                            @else
                                                <i class="fas fa-box-open text-2xl text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3">
                                                <h4 class="font-semibold text-gray-800">Order #{{ $order->order_number }}</h4>
                                                <span class="text-xs px-2 py-1 rounded text-white {{ $order->status === 'pending' ? 'bg-yellow-500' : ($order->status === 'cancelled' ? 'bg-red-500' : 'bg-gray-700') }}">{{ ucfirst($order->status) }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                                            @if($order->items->count())
                                                <p class="text-sm text-gray-700 mt-2">{{ $order->items->first()->product->name ?? 'Item' }} <span class="text-gray-500">x{{ $order->items->first()->quantity ?? 1 }}</span></p>
                                                <p class="text-xs text-gray-500 mt-1">{{ max(0, $order->items->count() - 1) }} more item(s)</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="text-lg font-bold text-gray-900">₱{{ number_format($order->total ?? $order->total_amount ?? 0, 2) }}</p>
                                        <div class="mt-3 flex items-center justify-end gap-2">
                                            <button data-order-id="{{ $order->id }}" class="order-details inline-block px-3 py-1 bg-white border text-sm rounded text-gray-700">Details</button>

                                            @if(in_array($order->status, \App\Models\Order::TRACKABLE_STATUSES, true))
                                                <a href="{{ route('tracking.show', $order) }}" class="inline-block px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold">
                                                    <i class="fas fa-truck mr-1"></i> Track
                                                </a>
                                            @endif

                                            @if($order->status === 'pending')
                                                <form method="POST" action="{{ route('order.cancel', $order) }}" onsubmit="return confirm('Cancel order {{ $order->order_number }}?');">
                                                    @csrf
                                                    <button type="submit" class="inline-block px-3 py-1 bg-red-600 text-white rounded text-sm">Cancel</button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('order.buy_again', $order) }}">
                                                @csrf
                                                <button type="submit" class="inline-block px-3 py-1 bg-blue-600 text-white rounded text-sm">Buy again</button>
                                            </form>

                                            @if($order->status === 'delivered')
                                                <form method="POST" action="{{ route('order.request_return', $order) }}" onsubmit="return confirm('Request a return for {{ $order->order_number }}?');">
                                                    @csrf
                                                    <button type="submit" class="inline-block px-3 py-1 bg-yellow-500 text-black rounded text-sm">Request Return</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-500 italic">No orders in this category.</div>
                @endif
            </div>
        @endforeach
    @else
        @forelse($orders as $order)
        <div class="border border-gray-100 rounded-lg p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                        @if($order->items->first() && $order->items->first()->product && $order->items->first()->product->image_url)
                            <img src="{{ $order->items->first()->product->image_url }}" alt="Product image for {{ $order->items->first()->product->name ?? 'order item' }}" class="object-cover w-full h-full" onerror="this.onerror=null;this.src='https://placehold.co/100x100/FF7F00/ffffff?text=No+Image';">
                        @else
                            <i class="fas fa-box-open text-2xl text-gray-300"></i>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h4 class="font-semibold text-gray-800">Order #{{ $order->order_number }}</h4>
                            <span class="text-xs px-2 py-1 rounded text-white {{ $order->status === 'pending' ? 'bg-yellow-500' : ($order->status === 'cancelled' ? 'bg-red-500' : 'bg-gray-700') }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                        @if($order->items->count())
                            <p class="text-sm text-gray-700 mt-2">{{ $order->items->first()->product->name ?? 'Item' }} <span class="text-gray-500">x{{ $order->items->first()->quantity ?? 1 }}</span></p>
                            <p class="text-xs text-gray-500 mt-1">{{ max(0, $order->items->count() - 1) }} more item(s)</p>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-lg font-bold text-gray-900">₱{{ number_format($order->total ?? $order->total_amount ?? 0, 2) }}</p>
                    <div class="mt-3 flex items-center justify-end gap-2">
                        <button data-order-id="{{ $order->id }}" class="order-details inline-block px-3 py-1 bg-white border text-sm rounded text-gray-700">Details</button>

                        @if($order->status === 'pending')
                            <form method="POST" action="{{ route('order.cancel', $order) }}" onsubmit="return confirm('Cancel order {{ $order->order_number }}?');">
                                @csrf
                                <button type="submit" class="inline-block px-3 py-1 bg-red-600 text-white rounded text-sm">Cancel</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('order.buy_again', $order) }}">
                            @csrf
                            <button type="submit" class="inline-block px-3 py-1 bg-blue-600 text-white rounded text-sm">Buy again</button>
                        </form>

                        @if($order->status === 'delivered')
                            <form method="POST" action="{{ route('order.request_return', $order) }}" onsubmit="return confirm('Request a return for {{ $order->order_number }}?');">
                                @csrf
                                <button type="submit" class="inline-block px-3 py-1 bg-yellow-500 text-black rounded text-sm">Request Return</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-600">
            <i class="fas fa-shopping-bag text-5xl mb-4 text-gray-300"></i>
            <p class="text-lg font-semibold">No orders yet</p>
            <p class="mt-2">Your recent purchases will appear here.</p>
        </div>
        @endforelse
    @endif
</div>
