<!-- Onboarding modal -->
<div x-data="{ show: {{ Auth::user()?->hasCompletedOnboarding() ? 'false' : 'true' }} }"
     x-show="show"
     x-cloak
     x-transition.opacity.duration.200ms
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/60 backdrop-blur-sm">
    <div @click.away="show = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="card max-w-lg w-full p-8 relative overflow-hidden">
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-seait-500 via-seait-400 to-amber-400"></div>
        <div class="flex items-center gap-3 mb-5">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3a1.5 1.5 0 013 0v5.5M9 14.5a1.5 1.5 0 01-3 0v-2a1.5 1.5 0 013 0v2z"/></svg>
                </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Welcome to StudHub!</h3>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            You're all set. Here's how to make the most of StudHub:
        </p>

        <div class="space-y-4 mb-6">
            <div class="flex items-start gap-3 p-3 rounded-xl bg-seait-50/50 dark:bg-seait-900/10">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-seait-400 to-seait-500 flex items-center justify-center text-white font-bold text-xs">1</div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm dark:text-gray-200">Upload resources</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Share reviewers, e-modules, and textbooks tagged by subject.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-xl bg-blue-50/50 dark:bg-blue-900/10">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center text-white font-bold text-xs">2</div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm dark:text-gray-200">Join program chat</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Connect with batchmates using @mentions.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50/50 dark:bg-emerald-900/10">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-xs">3</div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm dark:text-gray-200">Post requests</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Auto-routed to programs that teach your subject.</p>
                </div>
            </div>
        </div>

        <button @click="show = false" class="btn-primary w-full">
            Got it — let's go!
        </button>
    </div>
</div>