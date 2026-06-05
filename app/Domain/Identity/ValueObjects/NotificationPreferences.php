<?php

namespace App\Domain\Identity\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/** @implements Arrayable<string, mixed> */
class NotificationPreferences implements Arrayable
{
    /**
     * @param  array<int, int>  $mutedPrograms
     */
    public function __construct(
        public readonly bool $onlyUrgent = false,
        public readonly array $mutedPrograms = [],
        public readonly bool $digestEnabled = true,
    ) {}

    /**
     * Create from a raw array (e.g., from JSON column).
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            onlyUrgent: (bool) ($data['only_urgent'] ?? false),
            mutedPrograms: (array) ($data['muted_programs'] ?? []),
            digestEnabled: (bool) ($data['digest_enabled'] ?? true),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'only_urgent' => $this->onlyUrgent,
            'muted_programs' => $this->mutedPrograms,
            'digest_enabled' => $this->digestEnabled,
        ];
    }

    public function isProgramMuted(int $programId): bool
    {
        return in_array($programId, $this->mutedPrograms, true);
    }
}
