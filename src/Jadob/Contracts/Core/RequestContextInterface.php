<?php
declare(strict_types=1);

namespace Jadob\Contracts\Core;

use Jadob\Router\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
interface RequestContextInterface
{

    /**
     * After locking, it would be impossible to change context props.
     *
     * Locking should happen before controller dispatching:
     * Request -> Routing -> Events -> Locking -> Controller -> Response
     *
     * @return mixed
     */
    public function lock();

    public function getRequest(): Request;

    public function getRoute(): Route;

    public function getSession(): SessionInterface;

    public function getUser(): ?UserInterface;
}