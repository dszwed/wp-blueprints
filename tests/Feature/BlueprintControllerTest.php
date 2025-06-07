<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Blueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Enums\PhpVersion;
use App\Enums\WordpressVersion;
use Inertia\Testing\AssertableInertia as Assert;

class BlueprintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_blueprint_via_web(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/blueprints', [
                'name' => 'Test Web Blueprint',
                'status' => 'public',
                'landingPage' => '/wp-admin/',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_2->value,
                    'wp' => WordpressVersion::V6_8->value,
                ],
                'features' => [
                    'networking' => true,
                ],
                'steps' => [
                    ['step' => 'install-plugin', 'plugin' => 'woocommerce'],
                ],
            ]);

        $response->assertStatus(200);
        
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Generator')
            ->has('data')
            ->where('data.name', 'Test Web Blueprint')
            ->where('data.status', 'public')
            ->where('data.is_anonymous', false)
            ->has('data.id')
        );

        $this->assertDatabaseHas('blueprints', [
            'name' => 'Test Web Blueprint',
            'status' => 'public',
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->post('/blueprints', [
            'name' => 'Test Blueprint',
            'status' => 'public',
            'landingPage' => '/wp-admin/',
            'preferredVersions' => [
                'php' => PhpVersion::V8_2->value,
                'wp' => WordpressVersion::V6_8->value,
            ],
            'features' => [
                'networking' => true,
            ],
            'steps' => [
                ['step' => 'install-plugin', 'plugin' => 'woocommerce'],
            ],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_validation_fails_with_invalid_data_via_web(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/generator')
            ->post('/blueprints', [
                'name' => '', // Invalid: empty name
                'status' => 'invalid', // Invalid: not in enum
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

        $response->assertRedirect('/generator');
        $response->assertSessionHasErrors([
            'name',
            'status', 
            'landingPage',
            'preferredVersions.php',
            'preferredVersions.wp',
            'features.networking',
            'steps',
        ]);
    }

    public function test_blueprint_creation_with_minimal_data(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/blueprints', [
                'name' => 'Minimal Blueprint',
                'status' => 'public',
                'landingPage' => '/wp-admin/',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_2->value,
                    'wp' => WordpressVersion::V6_8->value,
                ],
                'features' => [
                    'networking' => true,
                ],
                'steps' => [
                    ['step' => 'login', 'username' => 'admin', 'password' => 'password'],
                ],
            ]);

        $response->assertStatus(200);
        
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Generator')
            ->has('data')
            ->where('data.name', 'Minimal Blueprint')
            ->has('data.steps')
        );
    }

    public function test_blueprint_creation_with_complex_steps(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $complexSteps = [
            ['step' => 'install-plugin', 'plugin' => 'woocommerce'],
            ['step' => 'login', 'username' => 'admin', 'password' => 'password'],
            ['step' => 'install-plugin', 'plugin' => 'yoast-seo'],
        ];

        $response = $this->actingAs($user)
            ->post('/blueprints', [
                'name' => 'Complex Blueprint',
                'status' => 'public',
                'landingPage' => '/wp-admin/',
                'preferredVersions' => [
                    'php' => PhpVersion::V8_1->value,
                    'wp' => WordpressVersion::V6_7->value,
                ],
                'features' => [
                    'networking' => false,
                ],
                'steps' => $complexSteps,
            ]);

        $response->assertStatus(200);
        
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Generator')
            ->has('data')
            ->where('data.name', 'Complex Blueprint')
            ->has('data.steps', 3) // Should have 3 steps
        );
    }
} 