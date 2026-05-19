<?php

namespace App\Http\Controllers;

use App\Domain\Moderation\Actions\CreateReport;
use App\Domain\Moderation\Enums\ReportedType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;

class ReportController extends Controller
{
    public function store(HttpRequest $httpRequest, CreateReport $createReport): RedirectResponse
    {
        $user = $httpRequest->user();
        abort_unless($user !== null, 403);

        $validated = $httpRequest->validate([
            'reported_type' => ['required', 'string', 'in:' . implode(',', ReportedType::values())],
            'reported_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:64'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $createReport->handle(
                $user,
                ReportedType::from($validated['reported_type']),
                (int) $validated['reported_id'],
                $validated['reason'],
                $validated['notes'] ?? null
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }

        session()->flash('status', 'Report submitted. A moderator will review it.');

        return redirect()->back();
    }
}
