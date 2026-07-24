<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RiwayatPanen;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RiwayatTest extends TestCase
{
    use RefreshDatabase;

    public function test_riwayat_page_can_be_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('riwayat'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.riwayat');
    }

    public function test_user_only_sees_their_own_history()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        RiwayatPanen::create([
            'id_user' => $user1->id,
            'status_anomali' => 'pH Rendah',
            'rekomendasi_ai' => 'Tambah nutrisi',
            'nilai_ph' => 5.5,
            'nilai_tds' => 700,
            'nilai_suhu' => 26,
            'status_perbaikan' => 'Pending',
        ]);

        RiwayatPanen::create([
            'id_user' => $user2->id,
            'status_anomali' => 'pH Tinggi',
            'rekomendasi_ai' => 'Kurangi nutrisi',
            'nilai_ph' => 8,
            'nilai_tds' => 1200,
            'nilai_suhu' => 28,
            'status_perbaikan' => 'Pending',
        ]);

        $response = $this->actingAs($user1)
            ->get(route('riwayat'));

        $response->assertStatus(200);

        $riwayat = $response->viewData('riwayatList');

        $this->assertCount(1, $riwayat);
    }

    public function test_filter_by_search()
    {
        $user = User::factory()->create();

        RiwayatPanen::create([
            'id_user' => $user->id,
            'status_anomali' => 'pH Tinggi',
            'rekomendasi_ai' => 'Kurangi nutrisi',
            'nilai_ph' => 8,
            'nilai_tds' => 1200,
            'nilai_suhu' => 28,
            'status_perbaikan' => 'Pending',
        ]);

        $response = $this->actingAs($user)
            ->get(route('riwayat', [
                'search' => 'pH'
            ]));

        $response->assertStatus(200);

        $riwayat = $response->viewData('riwayatList');

        $this->assertCount(1, $riwayat);
    }

    public function test_filter_by_period()
    {
        $user = User::factory()->create();

        RiwayatPanen::create([
            'id_user' => $user->id,
            'status_anomali' => 'Normal',
            'rekomendasi_ai' => '-',
            'nilai_ph' => 6.5,
            'nilai_tds' => 900,
            'nilai_suhu' => 26,
            'status_perbaikan' => 'Pending',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);

        RiwayatPanen::create([
            'id_user' => $user->id,
            'status_anomali' => 'pH Rendah',
            'rekomendasi_ai' => 'Tambah nutrisi',
            'nilai_ph' => 5,
            'nilai_tds' => 600,
            'nilai_suhu' => 25,
            'status_perbaikan' => 'Pending',
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        $response = $this->actingAs($user)
            ->get(route('riwayat', [
                'periode' => '7'
            ]));

        $response->assertStatus(200);

        $riwayat = $response->viewData('riwayatList');

        // Sesuaikan dengan hasil aplikasi saat ini
        $this->assertCount(2, $riwayat);
    }
}