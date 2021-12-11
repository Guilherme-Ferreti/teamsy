<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_model_has_a_tenant_id_on_the_migration()
    {
        try {
            $this->artisan('make:model Test -m');

            $filename = Arr::last(scandir(database_path('migrations/')));

            $migration_path = database_path('migrations/' . $filename);

            $model_path = app_path('Models/Test.php');
            
            $this->assertTrue(File::exists($migration_path));

            $this->assertStringContainsString(
                '$table->foreignId(\'tenant_id\')->constrained()->index();',
                File::get($migration_path)
            );

            $this->assertStringContainsString('BelongsToTenant', $model_path);
        } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
            File::delete($migration_path);
            File::delete($model_path);

            throw $e;
        }
    }

    public function test_a_user_can_only_see_users_in_the_same_tenant()
    {
        $tenantOne = Tenant::factory()->create();
        $tenantTwo = Tenant::factory()->create();

        $user = User::factory()->create(['tenant_id' => $tenantOne->id]);

        User::factory(9)->create(['tenant_id' => $tenantOne->id]);
        User::factory(10)->create(['tenant_id' => $tenantTwo->id]);

        auth()->login($user);

        $this->assertEquals(10, User::count());
    }

    public function test_a_user_can_only_create_a_user_in_his_tenant()
    {
        $tenantOne = Tenant::factory()->create();

        $user = User::factory()->create(['tenant_id' => $tenantOne->id]);

        auth()->login($user);
        
        $createdUser = User::factory()->create();

        $this->assertSame($user->tenant_id, $createdUser->tenant_id);
    }

    public function test_a_user_can_only_create_a_user_in_his_tenant_even_id_other_tenant_is_provided()
    {
        $tenantOne = Tenant::factory()->create();
        $tenantTwo = Tenant::factory()->create();

        $user = User::factory()->create(['tenant_id' => $tenantOne->id]);

        auth()->login($user);
        
        $createdUser = User::factory()->make();
        $createdUser->tenant_id = $tenantTwo->id;
        $createdUser->save();

        $this->assertSame($user->tenant_id, $createdUser->tenant_id);
    }
}
