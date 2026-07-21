<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class SensorControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function sensor_data_can_be_saved()
    {
        Http::fake([
            '*' => Http::response([
                'summary' => 'Normal',
                'risk' => 'low',
                'recommendation' => [],
            ], 200)
        ]);

        $response = $this->postJson('/api/sensor', [
            'ph' => 7.1,
            'suhu' => 27,
            'ec_tds' => 820
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('data_sensor', [
            'ph' => 7.1,
            'suhu' => 27,
            'ec_tds' => 820
        ]);
    }

    #[Test]
    public function sensor_reject_negative_data()
    {
        $response = $this->postJson('/api/sensor', [
            'ph' => -1,
            'suhu' => 25,
            'ec_tds' => 900
        ]);

        $response->assertStatus(422);
    }
}