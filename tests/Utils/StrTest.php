<?php

namespace App\Tests\Utils;

use App\Utils\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function testRandom()
    {
        //assertsame vérifie l'égalité et aussi le typage
        $this->assertSame(16,mb_strlen(Str::random()));
        $this->assertSame(6,mb_strlen(Str::random(6)));
        $this->assertSame(10,mb_strlen(Str::random(10)));
    }
}
