<x-mail::message>
# StudHub Daily Digest — {{ now()->format('F j, Y') }}

Hi {{ $displayName }},

Here's what's happening at StudHub today:

<x-mail::panel>
- **{{ $summary['request_count'] }}** new routed request(s) for your program
- **{{ $summary['chat_activity'] }}** new message(s) across your program chats
- **{{ $summary['active_programs'] }}** program(s) matched with active requests today
</x-mail::panel>

<x-mail::button :url="route('requests.index')">
Browse Open Requests
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>