{{-- resources/views/admin/users/_table.blade.php --}}
@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'unverified' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    ];
@endphp

<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead>
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">First Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
            @if($showDocument ?? false)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Documents</th>
            @endif
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
        </tr>
    </thead>

    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:text-gray-200 dark:divide-gray-700">
        @forelse($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-6 py-4">{{ $user->lname }}</td>
                <td class="px-6 py-4">{{ $user->fname }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $user->role_name }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$user->verification_status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($user->verification_status) }}
                    </span>
                </td>
                <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>

                @if($showDocument ?? false)
                    <td class="px-6 py-4">
                        <div class="flex flex-col space-y-1">
                            {{-- FRONT ID --}}
                            @if ($user->verification_document)
                                {{-- Note: If testing locally, change 's3' to 'public' below --}}
                                <a href="{{ Storage::disk('s3')->url($user->verification_document) }}" target="_blank"
                                   class="text-xs text-blue-600 underline hover:text-blue-800 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    View Front ID
                                </a>
                            @endif

                            {{-- BACK ID --}}
                            @if ($user->verification_document_back)
                                <a href="{{ Storage::disk('s3')->url($user->verification_document_back) }}" target="_blank"
                                   class="text-xs text-blue-600 underline hover:text-blue-800 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    View Back ID
                                </a>
                            @endif

                            @if (!$user->verification_document && !$user->verification_document_back)
                                <span class="text-xs text-gray-400 italic">No documents</span>
                            @endif
                        </div>
                    </td>
                @endif

              {{-- ACTIONS: Kebab Menu --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div 
                                x-show="open" 
                                @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                style="display: none;"
                            >
                                <div class="py-1">
                                    <a href="{{ route('admin.users.show', $user) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                        View Details
                                    </a>

                                    @if($user->verification_status === 'pending')
                                        <form action="{{ route('admin.users.verify', $user) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50 dark:hover:bg-gray-700 dark:text-green-400">
                                                Approve User
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.reject', $user) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 dark:hover:bg-gray-700 dark:text-yellow-400">
                                                Reject User
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-gray-700 dark:text-red-400">
                                            Delete User
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <p>No users found matching your criteria.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
</table>