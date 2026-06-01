<?php

namespace Tests\Unit;

use App\Support\DisplayTimezone;
use Carbon\Carbon;
use Tests\TestCase;

class DisplayTimezoneTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-05-31 12:00:00', 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_default_timezone_is_madrid(): void
    {
        $this->assertSame('Europe/Madrid', DisplayTimezone::DEFAULT);
    }

    public function test_formats_utc_timestamp_as_madrid_local_time(): void
    {
        config(['app.timezone' => 'Europe/Madrid']);

        $utc = Carbon::parse('2026-05-31 12:00:00', 'UTC');

        // Mayo: horario de verano (UTC+2)
        $this->assertSame('14:00', DisplayTimezone::formatTime($utc));
        $this->assertSame('31/05/2026', DisplayTimezone::formatDate($utc));
    }
}
