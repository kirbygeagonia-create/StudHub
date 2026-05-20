<?php

namespace App\Http\Controllers;

use App\Domain\Search\Actions\SearchGlobal;
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
}
