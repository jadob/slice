<?php

declare(strict_types=1);

namespace Jadob\EventDispatcher;

use Jadob\EventDispatcher\Exception\EventDispatcherException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Log\LoggerInterface;
use SplObjectStorage;
use function get_class;
use function microtime;
use function spl_object_hash;

/**
 * @see     https://www.php-fig.org/psr/psr-14/
 * @author  pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var int
     */
    private const DEFAULT_LISTENER_PRIORITY = 100;

    /**
     * @var ListenerProviderInterface[]
     */
    protected array $listeners = [];

    /**
     * @var  SplObjectStorage
     */
    protected SplObjectStorage $timestamps;

    /**
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger;

    /**
     * EventDispatcher constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->timestamps = new SplObjectStorage();
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     *
     * @see https://www.php-fig.org/psr/psr-14/#dispatcher
     * @throws EventDispatcherException
     */
    public function dispatch(object $event)
    {
        $className = get_class($event);
        $this->log(sprintf('Begin event %s dispatching.', $className));

        $handlers = [];

        foreach ($this->listeners as $listener) {
            $listenerPriority = self::DEFAULT_LISTENER_PRIORITY;
            $eventsFromListener = $listener->getListenersForEvent($event);
            /** @noinspection PhpParamsInspection */
            $eventsCount = count($eventsFromListener);

            if ($listener instanceof ListenerProviderPriorityInterface && $eventsCount > 0) {
                $listenerPriority = $listener->getListenerPriorityForEvent($event);
                if ($listenerPriority < 0) {
                    throw EventDispatcherException::negativeListenerPriority($listener, $event);
                }
            }

            $handlers[$listenerPriority][] = $eventsFromListener;
        }

        $handlersCount = 0;
        foreach ($handlers as $priority => $handlersByPriority) {
            $this->log(sprintf('Dispatching event %s for listeners with priority %s.', $className, $priority));

            foreach ($handlersByPriority as $listenersFromSingleObject) {
                foreach ($listenersFromSingleObject as $singleListener) {
                    $singleListener($event);
                    $handlersCount++;

                    if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                        $this->log(
                            sprintf('Event %s propagation has been stopped. Event has been consumed by %s listeners.', $className, $handlersCount)
                        );

                        return $event;
                    }
                }
            }
        }

        $this->log('Event ' . $className . ' has been consumed by ' . $handlersCount . ' listeners without interrupting.');
        return $event;
    }

    /**
     * @param string $message
     * @param array $context
     */
    private function log(string $message, array $context = []): void
    {
        if ($this->logger === null) {
            return;
        }

        $this->logger->info($message, $context);
    }

    /**
     * @param ListenerProviderInterface $provider
     * @return $this
     */
    public function addListener(ListenerProviderInterface $provider): EventDispatcher
    {
        $this->listeners[] = $provider;
        return $this;
    }
}