<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Illuminate\Support\Str;

class GlobalFiltersTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_retrieve_a_global_required_filter_value_from_context()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters.req_test', GlobalFiltersTestGlobalRequiredFilter::class);

        // First call without any value in session
        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals('default', currentSharpRequest()->globalFilterFor('req_test'));

        // Second call with a value in session
        $value = Str::random();
        session()->put('_sharp_retained_global_filter_req_test', $value);

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals($value, currentSharpRequest()->globalFilterFor('req_test'));
    }

    /** @test */
    public function we_can_set_a_global_filter_value_via_the_endpoint()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters.test', GlobalFiltersTestGlobalRequiredFilter::class);

        $this
            ->postJson('/sharp/api/filters/test', ['value' => 5])
            ->assertStatus(200);

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals(5, currentSharpRequest()->globalFilterFor('test'));

        $this
            ->postJson('/sharp/api/filters/test')
            ->assertStatus(200);

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals('default', currentSharpRequest()->globalFilterFor('test'));
    }

    /** @test */
    public function we_cant_set_an_invalid_global_filter_value_via_the_endpoint()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters.test', GlobalFiltersTestGlobalRequiredFilter::class);

        $this
            ->postJson('/sharp/api/filters/test', ['value' => 20])
            ->assertStatus(200);

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals('default', currentSharpRequest()->globalFilterFor('test'));
    }

    /** @test */
    public function we_can_get_global_filter_values_via_the_endpoint()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters.test', GlobalFiltersTestGlobalRequiredFilter::class);

        $this
            ->getJson('/sharp/api/filters')
            ->assertStatus(200)
            ->assertJson([
                'filters' => [
                    [
                        'key'      => 'test',
                        'multiple' => false,
                        'required' => true,
                        'default'  => 'default',
                    ],
                ],
            ]);
    }
}

class GlobalFiltersTestGlobalRequiredFilter implements GlobalRequiredFilter
{
    public function values(): array
    {
        return range(0, 10);
    }

    public function defaultValue()
    {
        return 'default';
    }
}
