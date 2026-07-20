<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\ScoringRule;
use App\Models\ScoringSystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ScoringSystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_scoring_rules_relationship(): void
    {
        // Given
        $system = ScoringSystem::factory()->create();
        $rule   = ScoringRule::factory()->create(['scoring_system_id' => $system->id]);

        // When
        $result = $system->scoringRules;

        // Then
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($rule));
    }
}
