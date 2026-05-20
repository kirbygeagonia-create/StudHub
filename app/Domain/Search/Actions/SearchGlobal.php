<?php

namespace App\Domain\Search\Actions;

use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Request;
use App\Models\User;
use Illuminate\Support\Collection;

class SearchGlobal
{
    /**
     * @return array{
     *     resources: Collection<int, LearningResource>,
     *     requests: Collection<int, Request>,
     *     messages: Collection<int, ChatMessage>,
     * }
     */
    public function handle(User $user, string $query, int $limit = 5): array
    {
        $query = trim($query);

        if ($query === '') {
            return [
                'resources' => collect(),
                'requests' => collect(),
                'messages' => collect(),
            ];
        }

        $resources = LearningResource::query()
            ->with(['owner:id,display_name,name', 'subject:id,code,name'])
            ->where('school_id', $user->school_id)
            ->where('availability', '!=', 'archived')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $requests = Request::query()
            ->with(['requester:id,display_name,name', 'subject:id,code,name'])
            ->whereHas('requester', function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $messages = ChatMessage::query()
            ->with(['sender:id,display_name,name', 'room:id,title,slug'])
            ->whereHas('room', function ($q) use ($user) {
                $q->where('program_id', $user->program_id);
            })
            ->where('body', 'like', '%' . $query . '%')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return [
            'resources' => $resources,
            'requests' => $requests,
            'messages' => $messages,
        ];
    }
}
