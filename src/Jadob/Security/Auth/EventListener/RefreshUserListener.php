<?php
declare(strict_types=1);

namespace Jadob\Security\Auth\EventListener;

use Jadob\Security\Auth\Event\UserEvent;
use Jadob\Security\Auth\User\RefreshableUserInterface;
use Jadob\Security\Auth\User\UserInterface;
use Jadob\Security\Supervisor\Authenticator;
use Psr\EventDispatcher\ListenerProviderInterface;

class RefreshUserListener implements ListenerProviderInterface
{

    protected Authenticator $supervisor;

    public function __construct(Authenticator $supervisor)
    {
        $this->supervisor = $supervisor;
    }


    public function getListenersForEvent(object $event): iterable
    {
        if($event instanceof UserEvent) {
            return [
                [$this, 'refreshUser']
            ];
        }

        return [];
    }

    public function refreshUser(UserEvent $event)
    {
        if(!$event->isTakenFromIdentityStorage()) {
            return;
        }

        $oldUser = $event->getUser();
        if(!($oldUser instanceof RefreshableUserInterface)) {
            return;
        }

        $userProvider = $this->supervisor->getUserProviderForSupervisor($event->getRequestSupervisor());
        /** @var UserInterface $freshUser */
        $freshUser = $userProvider->getUserById($oldUser->getId());
        $event->setUser($freshUser);
    }
}