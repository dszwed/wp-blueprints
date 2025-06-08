<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Blueprint;
use App\Models\User;
use App\Repositories\BlueprintRepository;
use App\Services\BlueprintService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class BlueprintServiceTest extends TestCase
{
    use RefreshDatabase;

    private BlueprintService $blueprintService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $blueprintRepository = new BlueprintRepository();
        $this->blueprintService = new BlueprintService($blueprintRepository);
    }

    public function testGetPaginatedListReturnsCorrectStructure(): void
    {
        // Arrange
        $user = User::factory()->create();
        $blueprint = Blueprint::factory()->create([
            'name' => 'Test Blueprint',
            'description' => 'Test Description',
            'status' => 'public',
            'php_version' => '8.1',
            'wordpress_version' => '6.0',
            'steps' => [
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'coblocks'
                    ]
                ],
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'contact-form-7'
                    ]
                ]
            ],
            'user_id' => $user->id,
            'is_anonymous' => false,
        ]);

        $filters = ['status' => 'public'];
        $perPage = 10;
        $page = 1;

        // Act
        $result = $this->blueprintService->getPaginatedList($filters, $perPage, $page);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        $this->assertInstanceOf(Collection::class, $result['data']);
        $this->assertCount(1, $result['data']);
        
        $blueprintData = $result['data']->first();
        $this->assertEquals($blueprint->id, $blueprintData['id']);
        $this->assertEquals('Test Blueprint', $blueprintData['name']);
        $this->assertEquals('Test Description', $blueprintData['description']);
        $this->assertEquals('public', $blueprintData['status']);
        $this->assertEquals('8.1', $blueprintData['php_version']);
        $this->assertEquals('6.0', $blueprintData['wordpress_version']);
        $expectedSteps = [
            [
                'step' => 'installPlugin',
                'pluginData' => [
                    'resource' => 'wordpress.org/plugins',
                    'slug' => 'coblocks'
                ]
            ],
            [
                'step' => 'installPlugin',
                'pluginData' => [
                    'resource' => 'wordpress.org/plugins',
                    'slug' => 'contact-form-7'
                ]
            ]
        ];
        $this->assertEquals($expectedSteps, $blueprintData['steps']);
        $this->assertFalse($blueprintData['is_anonymous']);
        
        $this->assertEquals(1, $result['meta']['current_page']);
        $this->assertEquals(10, $result['meta']['per_page']);
        $this->assertEquals(1, $result['meta']['total']);
    }

    public function testGetPaginatedListWithEmptyFilters(): void
    {
        // Arrange - no blueprints created

        // Act
        $result = $this->blueprintService->getPaginatedList();

        // Assert
        $this->assertIsArray($result);
        $this->assertInstanceOf(Collection::class, $result['data']);
        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['meta']['total']);
        $this->assertEquals(1, $result['meta']['current_page']);
        $this->assertEquals(15, $result['meta']['per_page']);
    }

    public function testGetPaginatedListWithFilters(): void
    {
        // Arrange
        Blueprint::factory()->create(['status' => 'public']);
        Blueprint::factory()->create(['status' => 'private']);
        Blueprint::factory()->create(['status' => 'public']);

        $filters = ['status' => 'public'];

        // Act
        $result = $this->blueprintService->getPaginatedList($filters);

        // Assert
        $this->assertCount(2, $result['data']);
        $this->assertEquals(2, $result['meta']['total']);
    }

    public function testCreateWithUser(): void
    {
        // Arrange
        $user = User::factory()->create();

        $data = [
            'name' => 'Test Blueprint',
            'description' => 'Test Description',
            'status' => 'public',
            'steps' => [
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'woocommerce'
                    ]
                ],
                [
                    'step' => 'installTheme',
                    'themeData' => [
                        'resource' => 'wordpress.org/themes',
                        'slug' => 'twentytwentyfour'
                    ]
                ]
            ],
            'preferredVersions' => [
                'php' => '8.1',
                'wp' => '6.0',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data, $user);

        // Assert
        $this->assertInstanceOf(Blueprint::class, $result);
        $this->assertEquals('Test Blueprint', $result->name);
        $this->assertEquals('Test Description', $result->description);
        $this->assertEquals('public', $result->status);
        $expectedSteps = [
            [
                'step' => 'installPlugin',
                'pluginData' => [
                    'resource' => 'wordpress.org/plugins',
                    'slug' => 'woocommerce'
                ]
            ],
            [
                'step' => 'installTheme',
                'themeData' => [
                    'resource' => 'wordpress.org/themes',
                    'slug' => 'twentytwentyfour'
                ]
            ]
        ];
        $this->assertEquals($expectedSteps, $result->steps);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertFalse($result->is_anonymous);
        $this->assertEquals('8.1', $result->php_version);
        $this->assertEquals('6.0', $result->wordpress_version);
        
        // Verify it was saved to database
        $this->assertDatabaseHas('blueprints', [
            'name' => 'Test Blueprint',
            'user_id' => $user->id,
            'is_anonymous' => false,
        ]);
    }

    public function testCreateWithoutUser(): void
    {
        // Arrange
        $data = [
            'name' => 'Anonymous Blueprint',
            'description' => 'Anonymous Description',
            'status' => 'private',
            'steps' => [
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'yoast-seo'
                    ]
                ]
            ],
            'preferredVersions' => [
                'php' => '8.2',
                'wp' => '6.5',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data);

        // Assert
        $this->assertInstanceOf(Blueprint::class, $result);
        $this->assertEquals('Anonymous Blueprint', $result->name);
        $this->assertEquals('Anonymous Description', $result->description);
        $this->assertEquals('private', $result->status);
        $expectedSteps = [
            [
                'step' => 'installPlugin',
                'pluginData' => [
                    'resource' => 'wordpress.org/plugins',
                    'slug' => 'yoast-seo'
                ]
            ]
        ];
        $this->assertEquals($expectedSteps, $result->steps);
        $this->assertNull($result->user_id);
        $this->assertTrue($result->is_anonymous);
        $this->assertEquals('8.2', $result->php_version);
        $this->assertEquals('6.5', $result->wordpress_version);
        
        // Verify it was saved to database
        $this->assertDatabaseHas('blueprints', [
            'name' => 'Anonymous Blueprint',
            'user_id' => null,
            'is_anonymous' => true,
        ]);
    }

    public function testCreateWithoutDescriptionAndPreferredVersions(): void
    {
        // Arrange
        $user = User::factory()->create();

        $data = [
            'name' => 'Minimal Blueprint',
            'status' => 'public',
            'steps' => [
                [
                    'step' => 'writeFile',
                    'path' => '/wp-content/mu-plugins/site-config.php',
                    'contents' => '<?php\n// Site configuration\ndefine("WP_DEBUG", true);'
                ]
            ],
            'preferredVersions' => [
                'php' => '8.1',
                'wp' => '6.0',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data, $user);

        // Assert
        $this->assertInstanceOf(Blueprint::class, $result);
        $this->assertEquals('Minimal Blueprint', $result->name);
        $this->assertNull($result->description);
        $this->assertEquals('public', $result->status);
        $expectedSteps = [
            [
                'step' => 'writeFile',
                'path' => '/wp-content/mu-plugins/site-config.php',
                'contents' => '<?php\n// Site configuration\ndefine("WP_DEBUG", true);'
            ]
        ];
        $this->assertEquals($expectedSteps, $result->steps);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertFalse($result->is_anonymous);
        $this->assertEquals('8.1', $result->php_version);
        $this->assertEquals('6.0', $result->wordpress_version);
    }

    public function testCreateWithPartialPreferredVersions(): void
    {
        // Arrange
        $data = [
            'name' => 'Blueprint with PHP only',
            'status' => 'public',
            'steps' => [
                [
                    'step' => 'activatePlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'elementor'
                    ]
                ]
            ],
            'preferredVersions' => [
                'php' => '8.2',
                'wp' => '6.0',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data);

        // Assert
        $this->assertInstanceOf(Blueprint::class, $result);
        $this->assertEquals('8.2', $result->php_version);
        $this->assertEquals('6.0', $result->wordpress_version);
        $this->assertTrue($result->is_anonymous);
    }

    public function testGetPaginatedListWithMultipleFilters(): void
    {
        // Arrange
        Blueprint::factory()->create([
            'status' => 'public',
            'php_version' => '8.2',
            'wordpress_version' => '6.0',
        ]);
        Blueprint::factory()->create([
            'status' => 'public',
            'php_version' => '8.1',
            'wordpress_version' => '6.0',
        ]);
        Blueprint::factory()->create([
            'status' => 'private',
            'php_version' => '8.2',
            'wordpress_version' => '6.0',
        ]);

        $filters = [
            'status' => 'public',
            'php_version' => '8.2',
            'wordpress_version' => '6.0',
        ];

        // Act
        $result = $this->blueprintService->getPaginatedList($filters);

        // Assert
        $this->assertCount(1, $result['data']);
        $this->assertEquals(1, $result['meta']['total']);
    }

    public function testGetPaginatedListPagination(): void
    {
        // Arrange
        Blueprint::factory()->count(25)->create(['status' => 'public']);

        // Act
        $page1 = $this->blueprintService->getPaginatedList([], 10, 1);
        $page2 = $this->blueprintService->getPaginatedList([], 10, 2);

        // Assert
        $this->assertCount(10, $page1['data']);
        $this->assertEquals(1, $page1['meta']['current_page']);
        $this->assertEquals(10, $page1['meta']['per_page']);
        $this->assertEquals(25, $page1['meta']['total']);

        $this->assertCount(10, $page2['data']);
        $this->assertEquals(2, $page2['meta']['current_page']);
        $this->assertEquals(10, $page2['meta']['per_page']);
        $this->assertEquals(25, $page2['meta']['total']);

        // Verify different items on different pages
        $page1Ids = $page1['data']->pluck('id')->toArray();
        $page2Ids = $page2['data']->pluck('id')->toArray();
        $this->assertEmpty(array_intersect($page1Ids, $page2Ids));
    }

    public function testCreateWithEmptySteps(): void
    {
        // Arrange
        $data = [
            'name' => 'Empty Steps Blueprint',
            'status' => 'public',
            'steps' => [],
            'preferredVersions' => [
                'php' => '8.1',
                'wp' => '6.0',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data);

        // Assert
        $this->assertInstanceOf(Blueprint::class, $result);
        $this->assertEquals([], $result->steps);
        $this->assertTrue($result->is_anonymous);
    }

    public function testCreateBlueprintIsSavedToDatabaseCorrectly(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = [
            'name' => 'Database Test Blueprint',
            'description' => 'Testing database persistence',
            'status' => 'private',
            'steps' => [
                ['step' => 'install', 'plugin' => 'test-plugin'],
                ['step' => 'configure', 'setting' => 'test-setting'],
            ],
            'preferredVersions' => [
                'php' => '8.2',
                'wp' => '6.5',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data, $user);

        // Assert
        $this->assertDatabaseHas('blueprints', [
            'id' => $result->id,
            'name' => 'Database Test Blueprint',
            'description' => 'Testing database persistence',
            'status' => 'private',
            'php_version' => '8.2',
            'wordpress_version' => '6.5',
            'user_id' => $user->id,
            'is_anonymous' => false,
        ]);

        // Verify steps are stored as JSON
        $freshBlueprint = Blueprint::find($result->id);
        $this->assertEquals([
            ['step' => 'install', 'plugin' => 'test-plugin'],
            ['step' => 'configure', 'setting' => 'test-setting'],
        ], $freshBlueprint->steps);
    }

    public function testCreateWithComplexStepsSchema(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = [
            'name' => 'Complex Blueprint',
            'description' => 'Testing complex steps schema',
            'status' => 'public',
            'steps' => [
                [
                    'step' => 'installPlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'woocommerce'
                    ]
                ],
                [
                    'step' => 'installTheme',
                    'themeData' => [
                        'resource' => 'wordpress.org/themes',
                        'slug' => 'storefront'
                    ]
                ],
                [
                    'step' => 'writeFile',
                    'path' => '/wp-content/mu-plugins/custom-config.php',
                    'contents' => '<?php\n// Custom configuration\ndefine("CUSTOM_SETTING", true);'
                ],
                [
                    'step' => 'activatePlugin',
                    'pluginData' => [
                        'resource' => 'wordpress.org/plugins',
                        'slug' => 'woocommerce'
                    ]
                ],
                [
                    'step' => 'activateTheme',
                    'themeData' => [
                        'resource' => 'wordpress.org/themes',
                        'slug' => 'storefront'
                    ]
                ],
                [
                    'step' => 'runPHP',
                    'code' => 'update_option("blogname", "My Blueprint Site");'
                ],
                [
                    'step' => 'setSiteOptions',
                    'options' => [
                        'blogdescription' => 'A site built with WordPress Blueprint',
                        'start_of_week' => 1,
                        'timezone_string' => 'Europe/London'
                    ]
                ]
            ],
            'preferredVersions' => [
                'php' => '8.2',
                'wp' => '6.5',
            ],
        ];

        // Act
        $result = $this->blueprintService->create($data, $user);

        // Assert
        $this->assertInstanceOf(Blueprint::class, $result);
        $this->assertEquals('Complex Blueprint', $result->name);
        $this->assertEquals('Testing complex steps schema', $result->description);
        $this->assertEquals('public', $result->status);
        
        // Verify all steps are preserved
        $this->assertCount(7, $result->steps);
        
        // Test specific step types
        $installPluginStep = $result->steps[0];
        $this->assertEquals('installPlugin', $installPluginStep['step']);
        $this->assertEquals('wordpress.org/plugins', $installPluginStep['pluginData']['resource']);
        $this->assertEquals('woocommerce', $installPluginStep['pluginData']['slug']);
        
        $installThemeStep = $result->steps[1];
        $this->assertEquals('installTheme', $installThemeStep['step']);
        $this->assertEquals('wordpress.org/themes', $installThemeStep['themeData']['resource']);
        $this->assertEquals('storefront', $installThemeStep['themeData']['slug']);
        
        $writeFileStep = $result->steps[2];
        $this->assertEquals('writeFile', $writeFileStep['step']);
        $this->assertEquals('/wp-content/mu-plugins/custom-config.php', $writeFileStep['path']);
        $this->assertStringContainsString('CUSTOM_SETTING', $writeFileStep['contents']);
        
        $activatePluginStep = $result->steps[3];
        $this->assertEquals('activatePlugin', $activatePluginStep['step']);
        $this->assertEquals('woocommerce', $activatePluginStep['pluginData']['slug']);
        
        $activateThemeStep = $result->steps[4];
        $this->assertEquals('activateTheme', $activateThemeStep['step']);
        $this->assertEquals('storefront', $activateThemeStep['themeData']['slug']);
        
        $runPHPStep = $result->steps[5];
        $this->assertEquals('runPHP', $runPHPStep['step']);
        $this->assertStringContainsString('update_option', $runPHPStep['code']);
        
        $setSiteOptionsStep = $result->steps[6];
        $this->assertEquals('setSiteOptions', $setSiteOptionsStep['step']);
        $this->assertEquals('A site built with WordPress Blueprint', $setSiteOptionsStep['options']['blogdescription']);
        $this->assertEquals(1, $setSiteOptionsStep['options']['start_of_week']);
        $this->assertEquals('Europe/London', $setSiteOptionsStep['options']['timezone_string']);
        
        // Verify it was saved to database correctly
        $this->assertDatabaseHas('blueprints', [
            'name' => 'Complex Blueprint',
            'user_id' => $user->id,
            'status' => 'public',
            'php_version' => '8.2',
            'wordpress_version' => '6.5',
        ]);
        
        // Verify JSON structure is preserved in database
        $freshBlueprint = Blueprint::find($result->id);
        $this->assertCount(7, $freshBlueprint->steps);
        $this->assertEquals('installPlugin', $freshBlueprint->steps[0]['step']);
        $this->assertEquals('setSiteOptions', $freshBlueprint->steps[6]['step']);
    }
} 