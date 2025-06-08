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
            ->post('/blueprints', [
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
        $response = $this->post('/blueprints', [
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
        $response = $this->post('/blueprints', [
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
        $response = $this->post('/blueprints', [
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

        $response->assertStatus(302)
            ->assertSessionHasErrors(['preferredVersions.wp']);
            
        $errors = session('errors');
        $this->assertStringContainsString(
            'The WordPress version must be one of:',
            $errors->first('preferredVersions.wp')
        );
    }
} 