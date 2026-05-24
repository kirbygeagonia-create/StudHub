<x-app-layout>
    <x-page-header title="{{ __('Feedback') }}" />

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Found a bug? Have a feature idea? Let us know — your feedback helps improve StudHub for everyone at SEAIT.
                    </p>

                    <form method="POST" action="{{ route('feedback.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type"
                                class="mt-1 block w-full input-field">
                                <option value="feedback">General Feedback</option>
                                <option value="bug">Bug Report</option>
                                <option value="feature">Feature Request</option>
                                <option value="praise">Praise</option>
                                <option value="other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="body" :value="__('Your feedback')" />
                            <textarea id="body" name="body" rows="5"
                                class="mt-1 block w-full input-field"
                                placeholder="Describe what's on your mind..."
                                maxlength="2000" required>{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">5–2000 characters</p>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Submit Feedback') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>