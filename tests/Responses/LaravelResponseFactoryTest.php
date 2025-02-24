<?php

namespace Responses;

use League\Glide\Responses\LaravelResponseFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

class LaravelResponseFactoryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        self::assertInstanceOf(
            'League\Glide\Responses\LaravelResponseFactory',
            new LaravelResponseFactory()
        );
    }

    public function testCreate()
    {
        $this->cache = Mockery::mock('League\Flysystem\FilesystemOperator', function ($mock) {
            $mock->shouldReceive('mimeType')->andReturn('image/jpeg')->once();
            $mock->shouldReceive('fileSize')->andReturn(0)->once();
            $mock->shouldReceive('readStream');
        });

        $factory = new LaravelResponseFactory();
        $response = $factory->create($this->cache, '');

        self::assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        self::assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        self::assertEquals('0', $response->headers->get('Content-Length'));
        self::assertStringContainsString(gmdate('D, d M Y H:i', strtotime('+1 years')), $response->headers->get('Expires'));
        self::assertEquals('max-age=31536000, public', $response->headers->get('Cache-Control'));
    }
}
