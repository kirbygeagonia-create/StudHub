<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Search\Actions\SearchGlobal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(HttpRequest $httpRequest, SearchGlobal $searchGlobal): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $results = ['resources' => collect(), 'requests' => collect(), 'messages' => collect()];
        $query = '';

        if ($httpRequest->has('q')) {
            $query = trim((string) $httpRequest->get('q'));
            // Reject single/double character queries to prevent scraping
            if ($query !== '' && mb_strlen($query) < 3) {
                $query = '';
            }
            if ($query !== '') {
                $results = $searchGlobal->handle($user, $query);
            }
        }

        return view('search.index', [
            'query' => $query,
            'resources' => $results['resources'],
            'requests' => $results['requests'],
            'messages' => $results['messages'],
        ]);
    }

    public function inline(HttpRequest $httpRequest, SearchGlobal $searchGlobal): JsonResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $query = trim((string) $httpRequest->get('q', ''));
        if ($query === '' || mb_strlen($query) < 3) {
            return response()->json(['results' => []]);
        }

        $results = $searchGlobal->handle($user, $query, 3);

        $formatted = [
            'resources' => $results['resources']->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'type' => $r->type instanceof ResourceType ? $r->type->label() : $r->type,
                'url' => route('resources.show', $r),
            ])->values(),
            'requests' => $results['requests']->map(fn ($r) => [
                'id' => $r->id,
                'description' => mb_substr((string) $r->description, 0, 80),
                'url' => route('requests.show', $r),
            ])->values(),
            'messages' => $results['messages']->map(fn ($m) => [
                'id' => $m->id,
                'body' => mb_substr((string) $m->body, 0, 80),
                'room' => $m->room?->title ?? 'Chat',
                'url' => route('chat.show', $m->chat_room_id),
            ])->values(),
        ];

        return response()->json($formatted);
    }
}
