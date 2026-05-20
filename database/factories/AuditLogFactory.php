<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'actor_user_id' => null,
            'action' => 'created',
            'target_type' => 'resource',
            'target_id' => null,
            'metadata' => [],
            'created_at' => now(),
        ];
    }
}
