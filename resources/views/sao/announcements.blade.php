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
        </div>
    </div>
@endsection

@section('content')
    <div class="card p-6">
        <p class="text-center text-gray-400 py-8">Announcement management coming soon.</p>
    </div>
@endsection