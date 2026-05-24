<x-app-layout>
    <x-page-header title="My Lends" />

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                <h3 class="section-title mb-4">Lent Out</h3>

                @if ($lentOut->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No resources lent yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">When you lend a resource to someone, it will appear here.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700">
                        @foreach ($lentOut as $lend)
                            <x-lend-row :lend="$lend" variant="lent" />
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $lentOut->links() }}
                    </div>
                @endif
            </div>

            <div class="card p-6">
                <h3 class="section-title mb-4">Borrowed</h3>

                @if ($borrowed->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No resources borrowed yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">When you borrow a resource, it will appear here.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700">
                        @foreach ($borrowed as $lend)
                            <x-lend-row :lend="$lend" variant="borrowed" />
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $borrowed->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>