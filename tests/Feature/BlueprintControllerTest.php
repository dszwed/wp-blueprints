<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Blueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Enums\PhpVersion;
use App\Enums\WordpressVersion;

class BlueprintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_blueprint(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson('/blueprints', [
                'name' => 'Test Blueprint',
                'status' => 'public',
                'landingPage' => 'example.com',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_2->value,
                    'wp' => WordpressVersion::V6_8->value,
                ],
                'features' => [
                    'networking' => true,
                ],
                'steps' => [
                    ['step' => 'initialization'],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'status',
                    'php_version',
                    'wordpress_version',
                    'steps',
                    'created_at',
                    'updated_at',
                    'is_anonymous',
                ],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Test Blueprint',
                    'status' => 'public',
                    'steps' => [
                        ['step' => 'initialization'],
                    ],
                    'is_anonymous' => false,
                ],
            ]);
    }

    public function test_anonymous_user_can_create_blueprint(): void
    {
        $response = $this->postJson('/blueprints', [
            'name' => 'Anonymous Blueprint',
            'status' => 'private',
            'landingPage' => 'example.com',
            'preferredVersions' => [
                'php' => PhpVersion::V8_1->value,
                'wp' => WordpressVersion::V6_7->value,
            ],
            'features' => [
                'networking' => true,
            ],
            'steps' => [
                ['step' => 'initialization'],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'status',
                    'php_version',
                    'wordpress_version',
                    'steps',
                    'created_at',
                    'updated_at',
                    'is_anonymous',
                ],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Anonymous Blueprint',
                    'status' => 'private',
                    'steps' => [
                        ['step' => 'initialization'],
                    ],
                    'is_anonymous' => true,
                ],
            ]);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/blueprints', [
            'name' => '',
            'status' => 'invalid',
            'landingPage' => '',
            'preferredVersions' => [
                'php' => 'invalid',
                'wp' => 'invalid',
            ],
            'features' => [
                'networking' => 'not-a-boolean',
            ],
            'steps' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'status',
                'landingPage',
                'preferredVersions.php',
                'preferredVersions.wp',
                'features.networking',
                'steps',
            ]);
    }

    public function test_validation_error_response_format(): void
    {
        $response = $this->postJson('/blueprints', [
            'name' => 'Test Blueprint',
            'status' => 'public',
            'landingPage' => '/wp-admin/',
            'preferredVersions' => [
                'php' => '8.2',
                'wp' => 'latest', // Invalid version - not in enum
            ],
            'features' => [
                'networking' => true,
            ],
            'steps' => [
                ['step' => 'initialization'],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'preferredVersions.wp',
                ],
            ])
            ->assertJson([
                'message' => 'The WordPress version must be one of: 6.8, 6.7, 6.6, 6.5, 6.4, 6.3, 6.2, 6.1, 6.0, 5.9, 5.8, 5.7, 5.6, 5.5, 5.4, 5.3, 5.2, 5.1, 5.0.',
                'errors' => [
                    'preferredVersions.wp' => [
                        'The WordPress version must be one of: 6.8, 6.7, 6.6, 6.5, 6.4, 6.3, 6.2, 6.1, 6.0, 5.9, 5.8, 5.7, 5.6, 5.5, 5.4, 5.3, 5.2, 5.1, 5.0.',
                    ],
                ],
            ]);
    }
} 