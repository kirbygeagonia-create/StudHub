<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAttachmentController extends Controller
{
    public function download(Request $request, ChatMessage $message): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        // Verify user can view the room (same program/school check as ChatController)
        $room = $message->room;
        if ($room->school_id !== $user->school_id) {
            abort(403, 'You cannot access attachments from this chat room.');
        }
        if ($room->program_id !== null && $room->program_id !== $user->program_id) {
            abort(403, 'This attachment is restricted to a different program.');
        }
        if ($room->year_level !== null && $room->year_level !== $user->year_level) {
            abort(403, 'This attachment is restricted to a different year level.');
        }

        // Verify the message has an attachment
        abort_unless($message->hasAttachment(), 404, 'No attachment found.');

        // Extract the relative path from the URL
        $path = str_replace('/storage/', '', $message->attachment_url);

        // Verify file exists
        abort_unless(Storage::disk('public')->exists($path), 404, 'File not found.');

        $fileName = $message->attachment_name ?? basename($path);

        return Storage::disk('public')->download($path, $fileName);
    }
}
