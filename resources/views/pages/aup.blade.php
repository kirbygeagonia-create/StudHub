<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Acceptable Use Policy
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">Effective date: {{ now()->format('F Y') }}</p>
                <div class="space-y-4 text-sm text-gray-600">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">1. Purpose</h3>
                        <p>StudHub is an academic resource exchange for SEAIT students. All uploaded materials must be for educational use within SEAIT.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">2. Prohibited Content</h3>
                        <p>Do not upload or share material that is:</p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>Copyrighted without permission from the copyright holder</li>
                            <li>Offensive, harassing, or discriminatory</li>
                            <li>Spam, phishing, or commercial advertising</li>
                            <li>Malware or executable files</li>
                            <li>Explicit or adult content</li>
                            <li>Containing personal information of others without consent</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">3. User Conduct</h3>
                        <p>All users are expected to:</p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>Use their real SEAIT identity — no anonymous or fake accounts</li>
                            <li>Report inappropriate content using the Report button</li>
                            <li>Not abuse the request system (spam requests, fake offers)</li>
                            <li>Not misuse chat rooms for non-academic purposes</li>
                            <li>Respect moderators and follow their instructions</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">4. Consequences</h3>
                        <p>Violations may result in:</p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>Content removal and karma deduction</li>
                            <li>Temporary or permanent account suspension</li>
                            <li>Referral to academic or administrative authorities</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">5. Privacy</h3>
                        <p>StudHub displays your display name and program for resource attribution. Your email and personal details are never shared publicly. Downloaded resources are watermarked with the downloader's identity.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">6. Changes</h3>
                        <p>This policy may be updated. Continued use after changes constitutes acceptance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>