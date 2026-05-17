<?php

namespace App\Http\Controllers;

use App\Domain\Requests\Actions\AcceptOffer;
use App\Domain\Requests\Actions\CreateOffer;
use App\Domain\Requests\Actions\CreateRequest;
use App\Domain\Requests\Actions\RouteRequest;
use App\Domain\Requests\Enums\OfferStatus;
use App\Domain\Requests\Enums\RequestStatus;
use App\Domain\Requests\Enums\RequestUrgency;
use App\Models\LearningResource;
use App\Models\Offer;
use App\Models\Program;
use App\Models\Request;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $query = Request::with(['requester:id,display_name,name,program_id', 'subject:id,code,name'])
            ->whereIn('status', RequestStatus::openValues())
            ->orderByDesc('created_at');

        if ($httpRequest->filled('subject_id')) {
            $query->where('subject_id', (int) $httpRequest->get('subject_id'));
        }

        if ($httpRequest->filled('urgency')) {
            $query->where('urgency', $httpRequest->get('urgency'));
        }

        if ($httpRequest->filled('type_wanted')) {
            $query->where('type_wanted', $httpRequest->get('type_wanted'));
        }

        $requests = $query->paginate(20);

        $subjects = Subject::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return view('requests.index', [
            'requests' => $requests,
            'subjects' => $subjects,
            'urgencies' => RequestUrgency::cases(),
            'types' => \App\Domain\Catalog\Enums\ResourceType::cases(),
            'filters' => $httpRequest->only(['subject_id', 'urgency', 'type_wanted']),
        ]);
    }

    public function create(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $subjects = Subject::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return view('requests.create', [
            'subjects' => $subjects,
            'urgencies' => RequestUrgency::cases(),
            'types' => \App\Domain\Catalog\Enums\ResourceType::cases(),
        ]);
    }

    public function store(HttpRequest $httpRequest, CreateRequest $createAction, RouteRequest $routeAction): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'type_wanted' => ['required', 'string', 'in:' . implode(',', \App\Domain\Catalog\Enums\ResourceType::values())],
            'urgency' => ['required', 'string', 'in:' . implode(',', RequestUrgency::values())],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $request = $createAction->handle($user, $validated);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        $routeAction->handle($request);

        session()->flash('status', 'Request posted. Matching programs will be notified.');

        return redirect()->route('requests.show', $request);
    }

    public function show(HttpRequest $httpRequest, Request $request): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        if ($request->requester->school_id !== $user->school_id) {
            abort(404);
        }

        $request->load([
            'requester:id,display_name,name,program_id',
            'subject:id,code,name',
            'offers' => fn ($q) => $q->with(['offerer:id,display_name,name,program_id', 'resource:id,title,type']),
            'routes.program:id,code,name',
        ]);

        $userResources = LearningResource::where('owner_user_id', $user->id)
            ->where('subject_id', $request->subject_id)
            ->where('type', $request->type_wanted)
            ->where('availability', '!=', 'archived')
            ->get(['id', 'title']);

        return view('requests.show', [
            'request' => $request,
            'userResources' => $userResources,
        ]);
    }

    public function storeOffer(HttpRequest $httpRequest, Request $request, CreateOffer $createOffer): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'resource_id' => ['nullable', 'integer', 'exists:resources,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $createOffer->handle($user, $request, $validated);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'Your offer has been submitted.');

        return redirect()->route('requests.show', $request);
    }

    public function acceptOffer(HttpRequest $httpRequest, Request $request, Offer $offer, AcceptOffer $acceptOffer): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        try {
            $acceptOffer->handle($user, $request, $offer);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'Offer accepted! You have been matched.');

        return redirect()->route('requests.show', $request);
    }
}