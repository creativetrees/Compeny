<?php

namespace Tests\Unit;

use App\Support\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function test_phone_id_normalizes_every_input_shape_to_one_format(): void
    {
        $expected = '+62 812 1235 0164';

        $this->assertSame($expected, Format::phoneId('081212350164'));
        $this->assertSame($expected, Format::phoneId('+6281212350164'));
        $this->assertSame($expected, Format::phoneId('6281212350164'));
        $this->assertSame($expected, Format::phoneId('0812-1235-0164'));
        $this->assertSame($expected, Format::phoneId('+62 812 1235 0164')); // idempotent
    }

    public function test_phone_id_is_null_when_empty(): void
    {
        $this->assertNull(Format::phoneId(null));
        $this->assertNull(Format::phoneId(''));
        $this->assertNull(Format::phoneId('-'));
    }

    public function test_nik_stores_16_raw_digits(): void
    {
        $this->assertSame('1234567890123456', Format::nik('1234-5678-9012-3456'));
        $this->assertSame('1234567890123456', Format::nik('1234567890123456'));
        $this->assertNull(Format::nik(null));
        $this->assertNull(Format::nik(''));
    }

    public function test_nik_masked_groups_into_4s(): void
    {
        $this->assertSame('1234-5678-9012-3456', Format::nikMasked('1234567890123456'));
        $this->assertSame('1234-5678-9012-3456', Format::nikMasked('1234-5678-9012-3456')); // idempotent
        $this->assertNull(Format::nikMasked(null));
    }

    public function test_phone_national_strips_country_code_and_leading_zero(): void
    {
        $this->assertSame('81212350164', Format::phoneNational('081212350164'));
        $this->assertSame('81212350164', Format::phoneNational('+6281212350164'));
        $this->assertSame('81212350164', Format::phoneNational('6281212350164'));
    }
}
