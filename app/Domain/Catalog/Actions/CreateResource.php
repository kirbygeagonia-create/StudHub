<?php

namespace App\Domain\Catalog\Actions;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use App\Domain\Catalog\Jobs\WatermarkResourceFile;
use App\Models\LearningResource;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CreateResource
{
    /**
     * @param  array{
     *     subject_id: int,
     *     type: ResourceType|string,
     *     title: string,
     *     description?: string|null,
     *     course_code?: string|null,
     *     year_taken?: int|null,
     *     year_level?: int|null,
     *     condition?: string|null,
     *     availability?: ResourceAvailability|string|null,
     *     visibility?: ResourceVisibility|string|null,
     * }  $data
     */
    public function handle(User $owner, array $data, ?UploadedFile $file = null): LearningResource
    {
        if (! $owner->school_id) {
            throw new RuntimeException('Owner must belong to a school.');
        }

        /** @var Subject|null $subject */
        $subject = Subject::where('id', $data['subject_id'])
            ->where('school_id', $owner->school_id)
            ->first();

        if ($subject === null) {
            throw new RuntimeException('Subject does not exist for this school.');
        }

        return DB::transaction(function () use ($owner, $subject, $data, $file): LearningResource {
            $filePayload = ['file_url' => null, 'file_mime' => null, 'file_size' => null];

            if ($file !== null) {
                $path = $file->store('resources', ['disk' => 'public']);

                $filePayload = [
                    'file_url' => $path,
                    'file_mime' => $file->getMimeType() ?: $file->getClientMimeType(),
                    'file_size' => $file->getSize() ?: null,
                ];
            }

            $resource = LearningResource::create([
                'school_id' => $owner->school_id,
                'owner_user_id' => $owner->id,
                'subject_id' => $subject->id,
                'program_id' => $owner->program_id,
                'type' => $data['type'] instanceof ResourceType ? $data['type']->value : $data['type'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'course_code' => $data['course_code'] ?? null,
                'year_taken' => $data['year_taken'] ?? null,
                'year_level' => $data['year_level'] ?? $owner->year_level,
                'condition' => $data['condition'] ?? null,
                'availability' => $this->availabilityValue($data['availability'] ?? ResourceAvailability::Available),
                'visibility' => $this->visibilityValue($data['visibility'] ?? ResourceVisibility::School),
                'file_url' => $filePayload['file_url'],
                'file_mime' => $filePayload['file_mime'],
                'file_size' => $filePayload['file_size'],
                'is_watermarked' => false,
                'published_at' => now(),
            ]);

            if ($filePayload['file_url'] !== null) {
                WatermarkResourceFile::dispatch($resource->id);
            }

            return $resource;
        });
    }

    private function availabilityValue(ResourceAvailability|string $value): string
    {
        return $value instanceof ResourceAvailability ? $value->value : $value;
    }

    private function visibilityValue(ResourceVisibility|string $value): string
    {
        return $value instanceof ResourceVisibility ? $value->value : $value;
    }

    public static function storageDisk(): string
    {
        return Storage::disk('public')->getConfig()['driver'] ?? 'local';
    }
}
