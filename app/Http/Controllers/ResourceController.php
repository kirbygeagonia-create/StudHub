<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\Actions\DownloadResourceFile;
use App\Domain\Catalog\Actions\SearchResources;
use App\Domain\Catalog\Actions\ToggleShelfItem;
use App\Domain\Catalog\Enums\ResourceType;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\Shelf;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceController extends Controller
{
    public function index(Request $request, SearchResources $search): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $filters = $request->only(['q', 'subject_id', 'type', 'program_id', 'year_level']);
        $resources = $search->handle($user, $filters);

        $subjects = Subject::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $programs = Program::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return view('resources.index', [
            'resources' => $resources,
            'subjects' => $subjects,
            'programs' => $programs,
            'types' => ResourceType::cases(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        return view('resources.create');
    }

    public function show(Request $request, LearningResource $resource): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        if ($resource->school_id !== $user->school_id) {
            throw new NotFoundHttpException;
        }

        if ($resource->isProgramOnly()
            && $resource->program_id !== $user->program_id
            && $resource->owner_user_id !== $user->id
        ) {
            throw new AccessDeniedHttpException('This resource is restricted to a different program.');
        }

        $resource->load(['owner:id,display_name,name,program_id', 'subject:id,code,name', 'program:id,code,name']);

        $isSaved = (new ToggleShelfItem)->isSaved($user, $resource);

        return view('resources.show', [
            'resource' => $resource,
            'isSaved' => $isSaved,
        ]);
    }

    public function shelf(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $shelf = $user->shelves()->first();

        $resources = collect();

        if ($shelf !== null) {
            $resources = $shelf->resources()
                ->with(['owner:id,display_name,name,program_id', 'subject:id,code,name', 'program:id,code'])
                ->orderByDesc('shelf_items.created_at')
                ->paginate(20);
        }

        return view('resources.shelf', [
            'shelf' => $shelf,
            'resources' => $resources,
        ]);
    }

    public function toggleSave(Request $request, LearningResource $resource, ToggleShelfItem $action): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        if ($resource->school_id !== $user->school_id) {
            throw new NotFoundHttpException;
        }

        $saved = $action->handle($user, $resource);

        session()->flash('status', $saved ? 'Resource saved to your shelf.' : 'Resource removed from your shelf.');

        return redirect()->back();
    }

    public function download(Request $request, LearningResource $resource, DownloadResourceFile $downloader): mixed
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        if ($resource->school_id !== $user->school_id) {
            throw new NotFoundHttpException;
        }

        if ($resource->isProgramOnly()
            && $resource->program_id !== $user->program_id
            && $resource->owner_user_id !== $user->id
        ) {
            throw new AccessDeniedHttpException('This resource is restricted to a different program.');
        }

        return $downloader->handle($user, $resource);
    }
}
