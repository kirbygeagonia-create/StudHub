<x-app-layout>
    <x-page-header title="{{ __('Notification Preferences') }}" />

    <div class="py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.notification-preferences.update') }}">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="only_urgent" value="1"
                                           class="rounded border-gray-300 dark:border-gray-600 text-seait-500 shadow-sm focus:ring-seait-400"
                                           @checked(old('only_urgent', $prefs['only_urgent'] ?? false))>
                                    <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">
                                        Only notify me about urgent requests
                                    </span>
                                </label>
                            </div>

                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="digest_enabled" value="1"
                                           class="rounded border-gray-300 dark:border-gray-600 text-seait-500 shadow-sm focus:ring-seait-400"
                                           @checked(old('digest_enabled', $prefs['digest_enabled'] ?? true))>
                                    <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">
                                        Receive daily digest emails
                                    </span>
                                </label>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Mute notifications from programs:
                                </p>
                                @php $muted = old('muted_programs', $prefs['muted_programs'] ?? []); @endphp
                                @foreach (\App\Models\Program::where('school_id', Auth::user()?->school_id)->where('is_active', true)->orderBy('code')->get(['id', 'code', 'name']) as $program)
                                    <label class="inline-flex items-center mr-4 mb-2">
                                        <input type="checkbox" name="muted_programs[]" value="{{ $program->id }}"
                                               class="rounded border-gray-300 dark:border-gray-600 text-seait-500 shadow-sm focus:ring-seait-400"
                                               @checked(in_array($program->id, $muted))>
                                        <span class="ms-1 text-sm text-gray-600 dark:text-gray-400">{{ $program->code }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>{{ __('Save Preferences') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>