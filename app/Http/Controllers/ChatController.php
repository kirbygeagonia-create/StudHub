<?php

namespace App\Http\Controllers;

use App\Domain\Chat\Enums\ChatRoomKind;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $rooms = ChatRoom::query()
            ->where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('kind', ChatRoomKind::Program->value)
                    ->where('program_id', $user->program_id)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('kind', ChatRoomKind::ProgramYear->value)
                            ->where('program_id', $user->program_id)
                            ->where('year_level', $user->year_level);
                    });
            })
            ->with(['members' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->orderBy('kind')
            ->orderBy('year_level')
            ->get();

        return view('chat.index', ['rooms' => $rooms]);
    }

    public function show(Request $request, ChatRoom $room): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        if (! $user->can('view', $room)) {
            throw new AccessDeniedHttpException('You cannot view this chat room.');
        }

        return view('chat.show', [
            'room' => $room->load('program.college'),
        ]);
    }
}
