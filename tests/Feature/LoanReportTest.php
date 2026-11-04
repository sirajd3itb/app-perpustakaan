<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoanReportTest extends TestCase
{
    use RefreshDatabase;
    public function test_loan_report_displays_data_from_api(): void
    {
        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        Http::fake([
            '*/api/loans*' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'member' => ['nama' => 'Budi Santoso'],
                        'petugas' => ['name' => 'Siti Rahmawati'],
                        'books' => [['judul' => 'Laskar Pelangi']],
                        'tanggal_pinjam' => '2026-10-15',
                        'tanggal_kembali' => '2026-10-22',
                        'status' => 'dipinjam',
                    ],
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get(route('loans.report'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Peminjaman');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Laskar Pelangi');
    }

    public function test_loan_report_handles_api_failure_gracefully(): void
    {
        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        Http::fake([
            '*/api/loans*' => Http::response([], 500),
        ]);

        $response = $this->actingAs($user)->get(route('loans.report'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada data peminjaman.');
    }
}
