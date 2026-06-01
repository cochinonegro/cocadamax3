<?php

namespace Tests\Unit;

use App\Filament\Support\ClienteFormatting;
use PHPUnit\Framework\TestCase;

class ClienteFormattingTest extends TestCase
{
    public function test_formats_phone_in_groups_of_three(): void
    {
        $this->assertSame('612 345 678', ClienteFormatting::formatPhone('612345678'));
        $this->assertSame('346 123 456 789 01', ClienteFormatting::formatPhone('+34 612-345-678-901'));
    }
}
