<?php
declare(strict_types=1);

namespace Jadob\Core;


use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestContextTest extends TestCase
{

    public function testContextCannotBeLockedTwice()
    {
        $context = new RequestContext('miki', Request::create('/test'), false);
        $context->lock();

        self::expectExceptionMessage('Context is already locked');
        self::expectException(\LogicException::class);
        $context->lock();
    }
}