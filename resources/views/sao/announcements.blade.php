@extends('layouts.admin')

@section('sidebar')
    @include('sao._sidebar')
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Campus Announcements
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Broadcast messages to all students and staff
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        {{-- Create form (2/5) --}}
        <div class="lg:col-span-2 card p-5">
            <h3 class="section-title mb-4">New Announcement</h3>
            <form method="POST" action="{{ route('sao.announcements.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" id="title" name="title" maxlength="255" required
                           class="input-field w-full text-sm" placeholder="e.g., Midterm Exam Schedule">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Body</label>
                    <textarea id="body" name="body" rows="5" maxlength="5000" required
                              class="input-field w-full text-sm" placeholder="Announcement details…"></textarea>
                    @error('body') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" checked
                               class="rounded border-gray-300 text-seait-500 focus:ring-seait-400">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Publish immediately</span>
                    </label>
                </div>
                <button type="submit" class="btn-primary text-xs">
                    Create Announcement
                </button>
            </form>
        </div>

        {{-- Announcements list (3/5) --}}
        <div class="lg:col-span-3 card p-5">
            <h3 class="section-title mb-4">All Announcements</h3>

            @if ($announcements->isEmpty())
                <x-empty-state
                    icon="announcements"
                    title="No announcements yet"
                    description="Create your first announcement using the form."
                />
            @else
                <div class="space-y-3">
                    @foreach ($announcements as $announcement)
                        <div class="p-4 rounded-xl border {{ $announcement->is_published ? 'border-emerald-200 dark:border-emerald-800/30 bg-emerald-50/30 dark:bg-emerald-900/10' : 'border-gray-200 dark:border-navy-700/50' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                            {{ $announcement->title }}
                                        </h4>
                                        @if ($announcement->is_published)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Published</span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400">Draft</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line line-clamp-3">
                                        {{ $announcement->body }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                        {{ $announcement->created_at->format('M d, Y g:i A') }}
                                        @if ($announcement->published_at)
                                            · Published {{ $announcement->published_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection