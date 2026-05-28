<x-app-layout>
    <x-page-header title="{{ __('Send Feedback') }}" />

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
    </div>

    <div class="py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-seait-50 to-transparent dark:from-seait-900/20 dark:to-transparent border-b border-gray-100 dark:border-navy-700/50">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Found a bug? Have a feature idea? Your feedback helps shape StudHub for every SEAIT student.
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('feedback.store') }}" class="space-y-5">
                        @csrf

                        {{-- Type selector --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <div x-data="{ selected: '{{ old('type', 'feedback') }}'}" class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach ([
                                    ['feedback', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>', 'Feedback'],
                                    ['bug', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12.75c1.148 0 2.278.08 3.396.237 1.037.146 1.866.966 1.866 2.013 0 1.08-.826 1.938-1.89 2.047C14.716 17.07 13.37 17 12 17c-1.37 0-2.716.07-3.872.047-1.064-.109-1.89-.967-1.89-2.047 0-1.047.83-1.867 1.866-2.013A24.438 24.438 0 0 1 12 12.75Zm0 0c2.883 0 5.647.508 8.207 1.44M12 12.75c-2.56 0-5.124-.508-7.684-1.44M12 3v3m0 0-2.25-2.25M12 6l2.25-2.25M3.316 16.5a3 3 0 0 1 .184-1.5m15.184 1.5a3 3 0 0 0-.184-1.5"/></svg>', 'Bug Report'],
                                    ['feature', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg>', 'Feature Request'],
                                    ['praise', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>', 'Praise'],
                                ] as [$val, $svg, $label])
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="{{ $val }}" class="sr-only" @if(old('type', 'feedback') === $val) checked @endif x-model="selected">
                                    <div :class="selected === '{{ $val }}' ? 'ring-2 ring-seait-400 border-seait-300 bg-seait-50 dark:bg-seait-900/20 dark:border-seait-700' : 'border-gray-200 dark:border-navy-700 hover:border-gray-300 dark:hover:border-navy-600'"
                                         class="flex flex-col items-center gap-1 px-3 py-2.5 rounded-xl border transition-all text-center">
                                        <span class="text-seait-500 dark:text-seait-400">{!! $svg !!}</span>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Message --}}
                        <div>
                            <label for="body" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Your message</label>
                            <textarea id="body" name="body" rows="5"
                                      class="input-field w-full"
                                      placeholder="Describe what's on your mind… be as specific as possible."
                                      maxlength="2000"
                                      required>{{ old('body') }}</textarea>
                            @error('body') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">5–2,000 characters</p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary">Submit Feedback</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>