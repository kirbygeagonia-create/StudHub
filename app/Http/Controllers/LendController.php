<?php

namespace App\Http\Controllers;

use App\Domain\Lends\Actions\RecordLend;
use App\Domain\Lends\Actions\ReturnResource;
use App\Domain\Lends\Enums\LendCondition;
use App\Models\Lend;
use App\Models\Offer;
use App\Models\ResourceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\View\View;

class LendController extends Controller
{
    public function index(HttpRequest $httpRequest): View
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $lentOut = Lend::with(['resource:id,title,type', 'toUser:id,display_name,name,program_id'])
            ->where('from_user_id', $user->id)
            ->orderByDesc('lent_at')
            ->paginate(10, ['*'], 'lent_page');

        $borrowed = Lend::with(['resource:id,title,type', 'fromUser:id,display_name,name,program_id'])
            ->where('to_user_id', $user->id)
            ->orderByDesc('lent_at')
            ->paginate(10, ['*'], 'borrowed_page');

        return view('lends.index', [
            'lentOut' => $lentOut,
            'borrowed' => $borrowed,
        ]);
    }

    public function record(HttpRequest $httpRequest, ResourceRequest $request, Offer $offer, RecordLend $recordLend): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'return_by' => ['nullable', 'date', 'after:today'],
        ]);

        try {
            $recordLend->handle($request, $offer, $user, $validated['return_by'] ?? null);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'Lend recorded. The resource has been marked as lent out.');

        return redirect()->route('lends.index');
    }

    public function return(HttpRequest $httpRequest, Lend $lend, ReturnResource $returnResource): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'condition' => ['nullable', 'string', 'in:' . implode(',', LendCondition::values())],
        ]);

        $condition = isset($validated['condition'])
            ? LendCondition::from($validated['condition'])
            : null;

        try {
            $returnResource->handle($user, $lend, $condition);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'Resource returned. Thank you!');

        return redirect()->route('lends.index');
    }
}
