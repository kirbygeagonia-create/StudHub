<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\Actions\SearchResources;
use App\Domain\Catalog\Enums\ResourceType;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\Subject;
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

        return view('resources.show', [
            'resource' => $resource->load(['owner:id,display_name,name,program_id', 'subject:id,code,name', 'program:id,code,name']),
        ]);
    }
}
