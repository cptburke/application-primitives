<?php


namespace CptBurke\Application\Reflection;


use Exception;
use ReflectionException;
use CptBurke\Application\Event\ApplicationEventSubscriber;
use ReflectionMethod;


class CallableExtractor
{


    /**
     * @param iterable $callables
     * @return callable[][]
     * @throws ReflectionException
     * @throws HandlerException
     */
    public function fromCallables(iterable $callables): array
    {
        $map = [];
        foreach ($callables as $callable) {
            $method = new ReflectionMethod($callable, '__invoke');
            if ($method->getNumberOfParameters() < 1) {
                throw new HandlerException('cannot map callable without invokable parameter');
            }
            $type = $method->getParameters()[0]->getType();
            if ($type === null) {
                throw new HandlerException('cannot map callable without typed parameter');
            }
            $param = $type->getName();
            $map[$param] = [$callable];
        }

        return $map;
    }


    /**
     * @param iterable $subscribers
     * @return callable[][]
     * @throws Exception
     */
    public function fromSubscribers(iterable $subscribers): array
    {
        $map = [];
        foreach ($subscribers as $subscriber) {
            if ( ! $subscriber instanceof ApplicationEventSubscriber) {
                throw new HandlerException('only instances of ApplicationEventSubscriber can subscribe');
            }
            $events = $subscriber::subscribedTo();
            foreach ($events as $event) {
                $handler = null;
                $methods = (new \ReflectionClass($subscriber))->getMethods();
                foreach ($methods as $method)  {
                    if ($method->getNumberOfParameters() !== 1) {
                        continue;
                    }
                    $type = $method->getParameters()[0]->getType();
                    if ($type === null) {
                        continue;
                    }

                    if ($type->getName() === $event) {
                        $handler = $method->getName();
                    }
                }

                if ($handler === null) {
                    throw new HandlerException("cannot find handler for $event");
                }

                if (!isset($map[$event])) {
                    $map[$event] = [];
                }
                $map[$event][] = [$subscriber, $handler];
            }
        }

        return $map;
    }

}
