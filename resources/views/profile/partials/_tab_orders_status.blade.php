<div id="orders-status" class="hidden overflow-hidden mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-6m3 6V9m3 8V7M4 21h16"></path>
                </svg>
                All Orders You Placed
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm max-w-2xl">
                Track every purchase you made as a buyer across all sellers.
            </p>
        </div>
        <div class="text-right">
            <div class="text-lg font-bold text-[#B59F84]">{{ $buyerOrders->count() }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Orders</div>
        </div>
    </div>

    @if ($buyerOrders->count() > 0)
        <div class="overflow-x-auto border border-[#E9DFC7] dark:border-gray-700 rounded-lg">
            <table class="min-w-full">
                <thead class="bg-[#F8F4EC] dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Order ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Item</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Payment Proof</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E9DFC7] dark:divide-gray-600">
                    @foreach ($buyerOrders as $order)
                        <tr class="hover:bg-[#F8F4EC] dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                #{{ $order->id }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-800 flex-shrink-0">
                                        @php
                                            $imageUrl = $order->product->first_image ?? $order->product->image_url ?? null;
                                        @endphp
                                        @if ($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $order->product->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                                                No image
                                            </div>
                                        @endif
                                    </div>
                                    <span>{{ $order->product->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                {{ $order->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if ($order->status === 'pending') bg-[#F1E9D2] text-[#8A7560]
                                    @elseif($order->status === 'approved') bg-[#F8F4EC] text-[#B59F84]
                                    @elseif($order->status === 'delivering') bg-[#E1D5B6] text-[#6B5B48]
                                    @elseif($order->status === 'completed') bg-[#F8F4EC] text-[#B59F84]
                                    @else bg-[#F4F2ED] text-[#8A7560] @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                @if ($order->proof)
                                    <button type="button"
                                        onclick="window.open('{{ Storage::disk('s3')->url($order->proof) }}', '_blank')"
                                        class="inline-flex items-center px-3 py-1 text-xs bg-[#B59F84] text-white rounded hover:bg-[#9C8770] transition-colors gap-1">
                                        View Proof
                                    </button>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">No proof uploaded</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-[#F8F4EC] dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-6m3 6V9m3 8V7M4 21h16"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">No Orders Yet</h4>
            <p class="text-gray-600 dark:text-gray-400 text-sm max-w-md mx-auto">
                When you place orders with this seller, you can track them here.
            </p>
        </div>
    @endif
</div>

