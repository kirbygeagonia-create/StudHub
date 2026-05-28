<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Verify your email</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Almost there! Check your inbox to activate your account.</p>
    </div>

    <div class="bg-gray-50 dark:bg-navy-700/40 rounded-xl px-4 py-3 mb-5 text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">We sent a verification link to</p>
        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 break-all">{{ auth()->user()?->email }}</p>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mb-5">
        Can't find it? Check your <strong>spam/junk folder</strong>. The link expires after 60 minutes.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm text-emerald-700 dark:text-emerald-300 text-center">
            ✓ A new verification link was sent to your email.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-ghost w-full text-sm text-gray-500 dark:text-gray-400">
                Use a different account
            </button>
        </form>
    </div>
</x-guest-layout>