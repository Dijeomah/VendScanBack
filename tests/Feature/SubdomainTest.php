<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Vendor;

class SubdomainTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_vendor_page_on_a_subdomain()
    {
        $vendor = Vendor::factory()->create([
            'name' => 'Test Vendor',
            'subdomain' => 'test-vendor',
        ]);

        $response = $this->get('http://test-vendor.vendscan.app');

        $response->assertStatus(200);
        $response->assertSee('Welcome to Test Vendor\'s page!');
    }
}
