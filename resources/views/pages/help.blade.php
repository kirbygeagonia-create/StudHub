<x-app-layout>
    <x-page-header title="Help & Guide" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Home
        </a>
    </div>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Getting Started</h3>
                <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-1">1. Complete your onboarding</h4>
                        <p>After registering with your SEAIT email, you'll be asked to pick your program and year level. This helps StudHub route resources correctly.</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-1">2. Post a resource</h4>
                        <p>Go to Resources &rarr; Post Resource. Upload PDFs, images, or documents. Your file is watermarked with your identity before it's shared.</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-1">3. Make a request</h4>
                        <p>Go to Resources &rarr; Request Board. Describe what you need (reviewer, textbook, past exam) and which subject. StudHub notifies students in programs that teach that subject.</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-1">4. Use chat</h4>
                        <p>Chat is per-program. Use @display_name to mention someone. Attach files up to 25 MB.</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">How to Ask for Resources</h3>
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <p>When posting a request, include these details to get faster responses:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>The exact <strong>subject code</strong> (e.g., <em>IT 211</em>, not "Programming")</li>
                        <li>What <strong>type</strong> of resource you need (reviewer, textbook, e-module, past exam)</li>
                        <li>Your <strong>urgency</strong> — low, normal, or urgent</li>
                        <li>Any specific details in the description (e.g., "preferably with answer keys")</li>
                    </ul>
                    <p class="mt-3">Once posted, StudHub's routing engine matches your request to programs that teach that subject. You'll receive offers from other students who have what you need.</p>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Karma &amp; Badges</h3>
                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>+5 karma</strong> — Upload a resource</p>
                    <p><strong>+5 karma</strong> — Your resource gets saved by someone</p>
                    <p><strong>+10 karma</strong> — Fulfill a request</p>
                    <p><strong>+2 karma</strong> — Your chat message marked helpful</p>
                    <p><strong>-5 karma</strong> — Confirmed report against you</p>
                    <p class="mt-3">Badge tiers: <span class="text-gray-600 dark:text-gray-300 font-medium">Seedling</span> (0), <span class="text-gray-600 dark:text-gray-300 font-medium">Bookworm</span> (25), <span class="text-gray-600 dark:text-gray-300 font-medium">Scribe</span> (75), <span class="text-gray-600 dark:text-gray-300 font-medium">Scholar</span> (150), <span class="text-gray-600 dark:text-gray-300 font-medium">Illuminator</span> (300), <span class="text-gray-600 dark:text-gray-300 font-medium">Pathfinder</span> (500), and beyond. Each tier is a permanent milestone — your progress never resets.</p>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contact</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    For issues or questions, message a moderator in your program's chat room or email
                    <a href="mailto:support@studhub.seait.local" class="text-seait-500 hover:underline">support@studhub.seait.local</a>.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>