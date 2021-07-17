<?php
declare(strict_types=1);

namespace Jadob\Security\Supervisor;

use Jadob\Core\RequestContext;
use Jadob\Security\Auth\Exception\AuthenticationException;
use Jadob\Security\Auth\Exception\InvalidCredentialsException;
use Jadob\Security\Auth\Exception\UserNotFoundException;
use Jadob\Security\Auth\IdentityStorage;
use Jadob\Security\Auth\UserProviderInterface;
use Jadob\Security\Supervisor\RequestSupervisor\RequestSupervisorInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function spl_object_hash;

/**
 * @author  pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class Supervisor
{
    /**
     * @var RequestSupervisorInterface[]
     */
    protected array $requestSupervisors = [];

    /**
     * @var UserProviderInterface[]
     */
    protected array $userProviders = [];

    protected IdentityStorage $identityStorage;

    protected LoggerInterface $logger;

    public function __construct(
        IdentityStorage $identityStorage,
        LoggerInterface $logger
    )
    {
        $this->identityStorage = $identityStorage;
        $this->logger = $logger;
    }

    /**
     * Finds out which supervisor can handle given request.
     * @param Request $request
     * @return RequestSupervisorInterface|null
     */
    public function matchRequestSupervisor(Request $request): ?RequestSupervisorInterface
    {
        $pathName = $request->attributes->get('path_name');

        foreach ($this->requestSupervisors as $requestSupervisor) {
            if ($requestSupervisor->supports($request)) {
                $this->logger->debug(
                    sprintf('Path "%s" is supported by "%s" supervisor.', $pathName, get_class($requestSupervisor))
                );
                return $requestSupervisor;
            }
        }

        $this->logger->debug(sprintf('Path "%s" is not supported by any supervisor.', $pathName));
        return null;
    }

    //@todo refactor
    public function addRequestSupervisor(string $name, RequestSupervisorInterface $requestSupervisor, UserProviderInterface $userProvider): void
    {
        $this->requestSupervisors[$name] = $requestSupervisor;
        $this->userProviders[spl_object_hash($requestSupervisor)] = $userProvider;
    }

    //@TODO refactor
    public function getUserProviderForSupervisor(RequestSupervisorInterface $supervisor): UserProviderInterface
    {
        return $this->userProviders[spl_object_hash($supervisor)];
    }

    /**
     * @return RequestSupervisorInterface[]
     */
    public function getRegisteredRequestSupervisors(): array
    {
        return $this->requestSupervisors;
    }

    public function handleStatefulRequest(RequestContext $context, RequestSupervisorInterface $requestSupervisor): ?Response
    {
    }

    public function handleStatelessRequest(
        RequestContext $context,
        RequestSupervisorInterface $requestSupervisor
    ): ?Response
    {
        $request = $context->getRequest();

        try {
            $credentials = $requestSupervisor->extractCredentialsFromRequest($request);
            $this->assertCredentialsPresence($credentials, get_class($requestSupervisor));

            $userProvider = $this->getUserProviderForSupervisor($requestSupervisor);
            $user = $requestSupervisor->getIdentityFromProvider($credentials, $userProvider);

            if (!$requestSupervisor->verifyIdentity($user, $credentials)) {
                throw InvalidCredentialsException::invalidCredentials();
            }

        } catch (AuthenticationException $exception) {
            return $requestSupervisor->handleAuthenticationFailure($exception, $request);
        }

        $this->identityStorage->setUser($user, $request->getSession(), get_class($requestSupervisor));
        return $requestSupervisor->handleAuthenticationSuccess($request, $user);
    }

    /**
     * @param string|array|null|bool $credentials
     * @param string $supervisorFqcn
     * @throws UserNotFoundException
     * @throws LogicException
     */
    protected function assertCredentialsPresence($credentials, string $supervisorFqcn)
    {
        //Verify that there are real user credentials
        if($credentials === true) {
            throw new LogicException(
                sprintf('%s::extractCredentialsFromRequest should return user credentials.', $supervisorFqcn)
            );
        }

        if (
            $credentials === null
            || $credentials === false
            || (is_countable($credentials) && count($credentials) === 0)
        ) {
            throw UserNotFoundException::emptyCredentials();
        }
    }
}