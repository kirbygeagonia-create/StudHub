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

        // Verify user is a member of the chat room
        $isMember = $message->room->members()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($isMember, 403, 'You are not a participant of this chat room.');

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
