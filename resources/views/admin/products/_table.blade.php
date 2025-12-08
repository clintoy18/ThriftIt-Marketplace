<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead>
        <tr>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Item</th>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Seller</th>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Category</th>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Price</th>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                approval_status</th>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Date</th>
            <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:text-gray-200 dark:divide-gray-700">
        @forelse($products as $product)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    {{ $product->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    {{ $product->user->fname }} {{ $product->user->lname }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    {{ $product->category->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ₱{{ number_format($product->price, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $product->approval_status === 'approved'
                            ? 'bg-green-100 text-green-800'
                            : ($product->approval_status === 'pending'
                                ? 'bg-yellow-100 text-yellow-800'
                                : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($product->approval_status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $product->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.products.show', $product) }}"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                        View
                    </a>

                    @if ($product->approval_status === 'pending')
                        {{-- Approve Button --}}
                        <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3"
                                onclick="return confirm('Approve this product?')">
                                Approve
                            </button>
                        </form>

                        {{-- Reject Button --}}
                        <button type="button"
                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                            onclick="openRejectModal({{ $product->id }})">
                            Reject
                        </button>
                    @else
                        {{-- Delete Button only if not pending --}}
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                onclick="return confirm('Delete this product?')">
                                Delete
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-gray-500 py-4">No products found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Modal to add admin notes --}}
<div id="rejectModal"
     class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-96">

        <h2 class="text-lg font-bold mb-3 text-gray-800 dark:text-gray-200">
            Reject Product
        </h2>

        <form id="rejectForm" method="POST">
            @csrf
            @method('PUT')

            <textarea name="admin_notes"
                      class="w-full p-2 border rounded"
                      rows="4"
                      placeholder="Add rejection notes..."></textarea>

            <div class="flex justify-end mt-4 space-x-2">
                <button type="button"
                        onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(productId) {
        const url = `/admin/products/${productId}/reject`; 
        document.getElementById('rejectForm').action = url;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }
</script>
