<?php
declare(strict_types=1);

namespace Jadob\Security\Supervisor;

/**
 * Supervisor listeners appends these attributes to request for internal use throughout the request.
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class RequestAttribute
{
    /**
     * Does supervisor for given request allows unauthenticated users to continue?
     * @var string
     */
    public const SUPERVISOR_ANONYMOUS_ALLOWED = '_supervisor.anonymous.allowed';
}