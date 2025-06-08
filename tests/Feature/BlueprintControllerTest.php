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
            ->post('/blueprint', [
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
                    [
                        'step' => 'installPlugin',
                        'pluginData' => [
                            'resource' => 'wordpress.org/plugins',
                            'slug' => 'hello-dolly'
                        ]
                    ],
                ],
            ]);

        $response->assertStatus(302)
            ->assertRedirect(route('generator'));
            
        // Verify blueprint was created in database
        $this->assertDatabaseHas('blueprints', [
            'name' => 'Test Blueprint',
            'status' => 'public',
            'user_id' => $user->id,
            'php_version' => PhpVersion::V8_2->value,
            'wordpress_version' => WordpressVersion::V6_8->value,
        ]);
        
        // Verify session has blueprint data
        $response->assertSessionHas('data');
        $sessionData = session('data');
        $this->assertEquals('Test Blueprint', $sessionData['name']);
        $this->assertEquals('public', $sessionData['status']);
        $this->assertFalse($sessionData['is_anonymous']);
    }

    public function test_anonymous_user_can_create_blueprint(): void
    {
        $response = $this->post('/blueprint', [
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
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'hello-dolly'
                    ]
                ],
            ],
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('generator'));
            
        // Verify blueprint was created in database
        $this->assertDatabaseHas('blueprints', [
            'name' => 'Anonymous Blueprint',
            'status' => 'private',
            'user_id' => null, // Anonymous user
            'php_version' => PhpVersion::V8_1->value,
            'wordpress_version' => WordpressVersion::V6_7->value,
        ]);
        
        // Verify session has blueprint data
        $response->assertSessionHas('data');
        $sessionData = session('data');
        $this->assertEquals('Anonymous Blueprint', $sessionData['name']);
        $this->assertEquals('private', $sessionData['status']);
        $this->assertTrue($sessionData['is_anonymous']);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $response = $this->post('/blueprint', [
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

        $response->assertStatus(302)
            ->assertSessionHasErrors([
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
        $response = $this->post('/blueprint', [
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
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'hello-dolly'
                    ]
                ],
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['preferredVersions.wp']);
            
        $errors = session('errors');
        $this->assertStringContainsString(
            'The WordPress version must be one of:',
            $errors->first('preferredVersions.wp')
        );
    }

    public function test_authenticated_user_can_update_their_own_blueprint(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        /** @var \App\Models\Blueprint $blueprint */
        $blueprint = Blueprint::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Blueprint',
            'status' => 'private',
            'php_version' => PhpVersion::V8_1->value,
            'wordpress_version' => WordpressVersion::V6_7->value,
        ]);

        $response = $this->actingAs($user)
            ->patch("/blueprint/{$blueprint->id}", [
                'name' => 'Updated Blueprint',
                'status' => 'public',
                'landingPage' => 'updated-example.com',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_2->value,
                    'wp' => WordpressVersion::V6_8->value,
                ],
                'features' => [
                    'networking' => false,
                ],
                'steps' => [
                    [
                        'step' => 'installTheme',
                        'themeData' => [
                            'resource' => 'wordpress.org/themes',
                            'slug' => 'twentytwentyfour'
                        ]
                    ],
                ],
            ]);

        $response->assertStatus(302)
            ->assertRedirect(route('generator'));
            
        // Verify blueprint was updated in database
        $this->assertDatabaseHas('blueprints', [
            'id' => $blueprint->id,
            'name' => 'Updated Blueprint',
            'status' => 'public',
            'user_id' => $user->id,
            'php_version' => PhpVersion::V8_2->value,
            'wordpress_version' => WordpressVersion::V6_8->value,
        ]);
        
        // Verify session has updated blueprint data
        $response->assertSessionHas('data');
        $sessionData = session('data');
        $this->assertEquals('Updated Blueprint', $sessionData['name']);
        $this->assertEquals('public', $sessionData['status']);
        $this->assertFalse($sessionData['is_anonymous']);
    }

    public function test_user_cannot_update_another_users_blueprint(): void
    {
        /** @var \App\Models\User $owner */
        $owner = User::factory()->create();
        
        /** @var \App\Models\User $otherUser */
        $otherUser = User::factory()->create();
        
        /** @var \App\Models\Blueprint $blueprint */
        $blueprint = Blueprint::factory()->create([
            'user_id' => $owner->id,
            'name' => 'Owner Blueprint',
        ]);

        $response = $this->actingAs($otherUser)
            ->patch("/blueprint/{$blueprint->id}", [
                'name' => 'Hacked Blueprint',
                'status' => 'public',
                'landingPage' => 'hacker.com',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_2->value,
                    'wp' => WordpressVersion::V6_8->value,
                ],
                'features' => [
                    'networking' => true,
                ],
                'steps' => [
                    [
                        'step' => 'runPHP',
                        'code' => 'echo "Hello World";'
                    ],
                ],
            ]);

        $response->assertStatus(403); // Forbidden
        
        // Verify blueprint was NOT updated
        $this->assertDatabaseHas('blueprints', [
            'id' => $blueprint->id,
            'name' => 'Owner Blueprint', // Original name unchanged
            'user_id' => $owner->id,
        ]);
        
        $this->assertDatabaseMissing('blueprints', [
            'id' => $blueprint->id,
            'name' => 'Hacked Blueprint',
        ]);
    }

    public function test_anonymous_user_cannot_update_any_blueprint(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        /** @var \App\Models\Blueprint $blueprint */
        $blueprint = Blueprint::factory()->create([
            'user_id' => $user->id,
            'name' => 'Protected Blueprint',
        ]);

        $response = $this->patch("/blueprint/{$blueprint->id}", [
            'name' => 'Anonymous Hack',
            'status' => 'public',
            'landingPage' => 'anonymous.com',
            'preferredVersions' => [
                'php' => PhpVersion::V8_2->value,
                'wp' => WordpressVersion::V6_8->value,
            ],
            'features' => [
                'networking' => true,
            ],
                            'steps' => [
                    [
                        'step' => 'writeFile',
                        'path' => '/wp-content/themes/test.txt',
                        'contents' => 'Test file content'
                    ],
                ],
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('login')); // Should redirect to login
        
        // Verify blueprint was NOT updated
        $this->assertDatabaseHas('blueprints', [
            'id' => $blueprint->id,
            'name' => 'Protected Blueprint', // Original name unchanged
            'user_id' => $user->id,
        ]);
    }

    public function test_anonymous_user_cannot_update_anonymous_blueprint(): void
    {
        /** @var \App\Models\Blueprint $blueprint */
        $blueprint = Blueprint::factory()->create([
            'user_id' => null, // Anonymous blueprint
            'name' => 'Anonymous Blueprint',
        ]);

        $response = $this->patch("/blueprint/{$blueprint->id}", [
            'name' => 'Updated Anonymous',
            'status' => 'public',
            'landingPage' => 'updated.com',
            'preferredVersions' => [
                'php' => PhpVersion::V8_2->value,
                'wp' => WordpressVersion::V6_8->value,
            ],
            'features' => [
                'networking' => true,
            ],
            'steps' => [
                [
                    'step' => 'setSiteOptions',
                    'options' => [
                        'blogname' => 'Test Site'
                    ]
                ],
            ],
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('login')); // Should redirect to login
        
        // Verify blueprint was NOT updated
        $this->assertDatabaseHas('blueprints', [
            'id' => $blueprint->id,
            'name' => 'Anonymous Blueprint', // Original name unchanged
            'user_id' => null,
        ]);
    }

    public function test_update_validation_fails_with_invalid_data(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        /** @var \App\Models\Blueprint $blueprint */
        $blueprint = Blueprint::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->patch("/blueprint/{$blueprint->id}", [
                'name' => '', // Empty name
                'status' => 'invalid-status',
                'landingPage' => '',
                'preferredVersions' => [
                    'php' => 'invalid-php',
                    'wp' => 'invalid-wp',
                ],
                'features' => [
                    'networking' => 'not-a-boolean',
                ],
                'steps' => 'not-an-array',
            ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'name',
                'status',
                'landingPage',
                'preferredVersions.php',
                'preferredVersions.wp',
                'features.networking',
                'steps',
            ]);
    }

    public function test_update_blueprint_not_found(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch('/blueprint/999999', [
                'name' => 'Non-existent Blueprint',
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
                    [
                        'step' => 'activatePlugin',
                        'pluginData' => [
                            'resource' => 'wordpress.org/plugins',
                            'slug' => 'hello-dolly'
                        ]
                    ],
                ],
            ]);

        $response->assertStatus(404);
    }

    public function test_partial_update_preserves_existing_data(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        /** @var \App\Models\Blueprint $blueprint */
        $blueprint = Blueprint::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
            'status' => 'private',
            'php_version' => PhpVersion::V8_1->value,
            'wordpress_version' => WordpressVersion::V6_7->value,
        ]);

        // Only update the name and status
        $response = $this->actingAs($user)
            ->patch("/blueprint/{$blueprint->id}", [
                'name' => 'Updated Name Only',
                'status' => 'public',
                'landingPage' => 'example.com',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_1->value, // Keep existing
                    'wp' => WordpressVersion::V6_7->value, // Keep existing
                ],
                'features' => [
                    'networking' => true,
                ],
                'steps' => [
                    [
                        'step' => 'activateTheme',
                        'themeData' => [
                            'resource' => 'wordpress.org/themes',
                            'slug' => 'twentytwentyfour'
                        ]
                    ],
                ],
            ]);

        $response->assertStatus(302)
            ->assertRedirect(route('generator'));
            
        // Verify only specified fields were updated
        $this->assertDatabaseHas('blueprints', [
            'id' => $blueprint->id,
            'name' => 'Updated Name Only',
            'status' => 'public',
            'user_id' => $user->id,
            'php_version' => PhpVersion::V8_1->value, // Preserved
            'wordpress_version' => WordpressVersion::V6_7->value, // Preserved
        ]);
    }
} 