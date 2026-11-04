<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    public function test_dashboard_displays_stats_from_api(): void
    {
        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        Http::fake([
            '*/api/stats' => Http::response([
                'total_buku' => 20,
                'total_anggota' => 15,
                'peminjaman_aktif' => 5,
            ], 200),
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('20');
        $response->assertSee('15');
        $response->assertSee('5');
    }

    public function test_dashboard_handles_api_failure_gracefully(): void
    {
        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        Http::fake([
            '*/api/stats' => Http::response([], 500),
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('0');
    }
}
