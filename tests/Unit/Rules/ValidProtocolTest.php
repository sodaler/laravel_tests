<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidProtocol;
use PHPUnit\Framework\TestCase;

class ValidProtocolTest extends TestCase
{
    /** @test */
    public function it_only_allows_http_or_https()
    {
        $validProtocol = new ValidProtocol();

        $this->assertTrue($validProtocol->passes('url', 'https://google.com'));
        $this->assertTrue($validProtocol->passes('url', 'http://google.com'));
        $this->assertFalse($validProtocol->passes('url', 'httpsgoogle.com'));
        $this->assertFalse($validProtocol->passes('url', 'https:google.com'));
        $this->assertFalse($validProtocol->passes('url', 'ftp://google.com'));
        $this->assertFalse($validProtocol->passes('url', 'https:/google.com'));
        $this->assertFalse($validProtocol->passes('url', 'googlehttps://.com'));
    }

    /** @test */
    public function it_returns_the_proper_message()
    {
        $validProtocol = new ValidProtocol();

        $this->assertEquals('The URL must be include the http protocol',
            $validProtocol->message());
    }
}
