<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DataSensor;
use App\Models\AnalisisAi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class RekomendasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_rekomendasi_page_can_be_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('rekomendasi'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.rekomendasi');
    }

    public function test_show_active_recommendation()
    {
        $user = User::factory()->create();

        $sensor = DataSensor::create([
            'ph' => 6.5,
            'suhu' => 27,
            'ec_tds' => 900,
            'status_valid' => true,
            'dibaca_pada' => now(),
        ]);

        AnalisisAi::create([
            'id_sensor' => $sensor->id,
            'kondisi_nutrisi' => 'Tidak Optimal',
            'status_tindakan' => 'belum',
            'waktu_analisis' => now(),
            'rekomendasi' => json_encode([
                'risk' => 'high',
                'summary' => 'Nutrisi terlalu tinggi',
                'recommendation' => [
                    'Kurangi nutrisi'
                ]
            ])
        ]);

        $response = $this->actingAs($user)
            ->get(route('rekomendasi'));

        $response->assertStatus(200);

        $response->assertViewHas('kondisiAktif');
    }

    public function test_user_in_cooldown()
    {
        $user = User::factory()->create();

        Cache::put(
            'rekomendasi_cooldown_' . $user->id,
            true,
            now()->addMinutes(5)
        );

        Cache::put(
            'rekomendasi_cooldown_expires_at_' . $user->id,
            now()->addMinutes(5),
            now()->addMinutes(5)
        );

        $response = $this->actingAs($user)
            ->get(route('rekomendasi'));

        $response->assertStatus(200);

        $response->assertViewHas('sedangCooldown', true);
    }

    public function test_recommendation_can_be_completed()
    {
        $user = User::factory()->create();

        $sensor = DataSensor::create([
            'ph' => 6.5,
            'suhu' => 27,
            'ec_tds' => 900,
            'status_valid' => true,
            'dibaca_pada' => now(),
        ]);

        $analisis = AnalisisAi::create([
            'id_sensor' => $sensor->id,
            'kondisi_nutrisi' => 'Tidak Optimal',
            'status_tindakan' => 'belum',
            'waktu_analisis' => now(),
            'rekomendasi' => json_encode([
                'risk' => 'medium',
                'summary' => 'Nutrisi kurang stabil',
                'recommendation' => [
                    'Lakukan pengecekan ulang'
                ]
            ])
        ]);

        $response = $this->actingAs($user)
            ->post(route('rekomendasi.selesai'), [
                'nutrisi_id' => $analisis->id_analisis
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('analisis_ai', [
            'id_analisis' => $analisis->id_analisis,
            'status_tindakan' => 'selesai'
        ]);

        $this->assertDatabaseHas('riwayat_anomali', [
            'id_user' => $user->id,
            'status_perbaikan' => 'Teratasi'
        ]);
    }
}