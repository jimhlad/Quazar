<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Yab\Quazar\Services\StripeService;

class SubscriptionsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(App\Models\User::class)->create([
            'id' => 1,
        ]);
        $this->role = factory(App\Models\Role::class)->create([
            'name' => 'admin',
        ]);

        $this->user->roles()->attach($this->role);
        $this->actingAs($this->user);

        factory(\Yab\Quazar\Models\Cart::class)->create();
        factory(\Yab\Quazar\Models\Product::class)->create();
        factory(\Yab\Quazar\Models\Plan::class)->create(['id' => 1]);
        factory(\Yab\Quazar\Models\Plan::class)->create(['id' => 2]);
        factory(\Yab\Quazar\Models\Plan::class)->create(['id' => 3]);
        factory(\Yab\Quazar\Models\Plan::class)->create(['id' => 4]);

        $stripe = Mockery::mock(\Stripe\Stripe::class);
        $plan = Mockery::mock(\Stripe\Plan::class);
        $refund = Mockery::mock(\Stripe\Refund::class);

        $planObject = Mockery::mock('StdClass');
        $planObject->shouldReceive('delete')->andReturn(true);

        $stripe->shouldReceive('setApiKey')->andReturn(true);
        $plan->shouldReceive('all')->andReturn((object) ['data' => []]);
        $plan->shouldReceive('create')->andReturn(true);
        $plan->shouldReceive('retrieve')->andReturn($planObject);
        $plan->shouldReceive('create')->andReturn(true);

        $refund->shouldReceive('create')->with(['charge' => 999])->andReturn(true);

        app()->bind(StripeService::class, function ($app) use ($stripe, $plan, $refund) {
            return new StripeService($stripe, $plan, $refund);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    */

    public function testIndex()
    {
        $response = $this->call('GET', 'quarx/plans');

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertViewHas('plans');
        $response->assertSee('Subscription Plans');
    }

    public function testCreate()
    {
        $response = $this->call('GET', 'quarx/plans/create');

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertSee('Name');
    }

    public function testEdit()
    {
        $response = $this->call('GET', 'quarx/plans/2/edit');

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertViewHas('plan');
        $response->assertSee('#');
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function testSearch()
    {
        $response = $this->call('POST', 'quarx/plans/search', ['term' => 'wtf']);

        $response->assertViewHas('plans');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $response = $this->call('PATCH', 'quarx/plans/4', [
            'name' => 'Batman Plan',
        ]);

        $this->assertDatabaseHas('plans', ['name' => 'Batman Plan']);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->call('DELETE', 'quarx/plans/1');
        $this->assertEquals(302, $response->getStatusCode());
    }
}
