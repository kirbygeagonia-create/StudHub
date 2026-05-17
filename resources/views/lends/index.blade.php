<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Lends
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Lent Out</h3>

                @if ($lentOut->isEmpty())
                    <p class="text-sm text-gray-500">You haven't lent any resources yet.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($lentOut as $lend)
                            <x-lend-row :lend="$lend" variant="lent" />
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $lentOut->links() }}
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Borrowed</h3>

                @if ($borrowed->isEmpty())
                    <p class="text-sm text-gray-500">You haven't borrowed any resources yet.</p>
                @else
                    <div class="divide-y divide-gray-100">
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