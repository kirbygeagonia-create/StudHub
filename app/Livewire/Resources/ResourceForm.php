<?php

namespace App\Livewire\Resources;

use App\Domain\Catalog\Actions\CreateResource;
use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ResourceForm extends Component
{
    use WithFileUploads;

    public ?int $subject_id = null;

    public string $type = ResourceType::Reviewer->value;

    public string $title = '';

    public ?string $description = null;

    public ?string $course_code = null;

    public ?int $year_taken = null;

    public ?int $year_level = null;

    public ?string $condition = null;

    public string $availability = 'available';

    public string $visibility = 'school';

    /** @var TemporaryUploadedFile|null */
    public $file = null;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user === null) {
            throw new AccessDeniedHttpException('You must be signed in.');
        }

        $this->year_level = $user->year_level;
    }

    /**
     * @return Collection<int, Subject>
     */
    #[Computed]
    public function subjects(): Collection
    {
        $user = auth()->user();
        abort_unless($user !== null, 403);

        return Subject::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    /**
     * @return array<int, ResourceType>
     */
    #[Computed]
    public function types(): array
    {
        return ResourceType::cases();
    }

    /**
     * @return array<int, ResourceVisibility>
     */
    #[Computed]
    public function visibilities(): array
    {
        return ResourceVisibility::cases();
    }

    /**
     * @return array<int, ResourceAvailability>
     */
    #[Computed]
    public function availabilities(): array
    {
        return ResourceAvailability::cases();
    }

    public function save(CreateResource $action): void
    {
        $this->title = trim($this->title);
        $this->description = $this->description !== null ? trim($this->description) : null;
        $this->course_code = $this->course_code !== null ? trim($this->course_code) : null;

        $validated = $this->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'type' => ['required', 'string', 'in:' . implode(',', ResourceType::values())],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'course_code' => ['nullable', 'string', 'max:32'],
            'year_taken' => ['nullable', 'integer', 'between:1980,2100'],
            'year_level' => ['nullable', 'integer', 'between:1,5'],
            'condition' => ['nullable', 'in:like_new,good,worn'],
            'availability' => ['required', 'string', 'in:' . implode(',', ResourceAvailability::values())],
            'visibility' => ['required', 'string', 'in:' . implode(',', ResourceVisibility::values())],
            'file' => ['nullable', 'file', 'max:25600', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf,application/zip,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain'],
        ]);

        $user = auth()->user();
        abort_unless($user !== null, 403);

        /** @var TemporaryUploadedFile|null $file */
        $file = $validated['file'] ?? null;
        unset($validated['file']);

        $resource = $action->handle($user, $validated, $file);

        session()->flash('status', 'Resource posted! (id: ' . $resource->id . ')');

        $this->redirectRoute('resources.show', $resource, navigate: false);
    }

    public function render(): View
    {
        return view('livewire.resources.form');
    }
}
