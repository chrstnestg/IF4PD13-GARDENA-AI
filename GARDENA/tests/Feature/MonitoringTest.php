<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DataSensor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MonitoringTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function monitoring_page_can_display_latest_sensor()
    {
        DataSensor::create([
            'ph' => 6.8,
            'suhu' => 26,
            'ec_tds' => 900,
            'status_valid' => true,
            'dibaca_pada' => now(),
        ]);

        $response = $this->get('/monitoring');

        $response->assertStatus(200);

        $response->assertViewHas('sensor');
    }
}