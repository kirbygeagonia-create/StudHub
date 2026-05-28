<?php

namespace App\Http\Controllers;

use App\Domain\Feedback\Actions\SubmitFeedback;
use App\Domain\Feedback\Enums\FeedbackType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function create(): View
    {
        return view('feedback.create');
    }

    public function store(Request $request, SubmitFeedback $submitFeedback): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:5', 'max:2000'],
            'type' => ['required', 'string', 'in:' . implode(',', FeedbackType::values())],
        ]);

        $submitFeedback->handle($user, $validated);

        session()->flash('status', 'Thank you for your feedback! We\'ll review it shortly.');

        return redirect()->route('feedback.create');
    }
}
