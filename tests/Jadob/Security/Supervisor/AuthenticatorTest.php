<?php
declare(strict_types=1);

namespace Jadob\Security\Supervisor;


use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;

class AuthenticatorTest extends TestCase
{

    public function testMatchingWillReturnNullWhenThereIsNoRequestSupervisors()
    {
        $sup = new Authenticator(new NullLogger());

        self::assertCount(0, $sup->getRegisteredRequestSupervisors());
        self::assertNull($sup->matchRequestSupervisor(Request::create('/test')));
    }


}