<?php


namespace spec\CptBurke\Application\Reflection;


use CptBurke\Application\Event\ApplicationEventSubscriber;
use CptBurke\Application\Reflection\CallableExtractor;
use CptBurke\Application\Reflection\HandlerException;
use PhpSpec\ObjectBehavior;
use ReflectionException;


class CallableExtractorSpec extends ObjectBehavior
{

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CallableExtractor::class);
    }

    public function it_needs_invokable(): void
    {
        $this->shouldThrow(ReflectionException::class)
            ->during('fromCallables', [[new class{}]])
        ;
    }

    public function it_needs_invokable_to_have_parameter(): void
    {
        $this->shouldThrow(HandlerException::class)
            ->during('fromCallables', [[new class{public function __invoke(){}}]])
        ;
    }

    public function it_needs_invokable_to_have_typed_parameter(): void
    {
        $this->shouldThrow(HandlerException::class)
            ->during('fromCallables', [[new class{public function __invoke($arg){}}]])
        ;
    }

    public function it_maps_callable_to_param(): void
    {
        $callable = new class { public function __invoke(int $test) {} };
        $res = $this->fromCallables([$callable]);
        $res->shouldHaveCount(1);
        $res->shouldHaveKeyWithValue('int', [$callable]);
    }

    public function it_maps_last_callable_to_param(): void
    {
        $callable = new class { public function __invoke(int $test) {} };
        $callable2 = new class { public function __invoke(int $test) {} };
        $res = $this->fromCallables([$callable, $callable2]);
        $res->shouldHaveCount(1);
        $res->shouldHaveKeyWithValue('int', [$callable2]);
    }

    public function it_only_maps_application_event_subscribers(): void
    {
        $this->shouldThrow(HandlerException::class)
            ->during('fromSubscribers', [[new class{}]])
        ;
    }

    public function it_throws_error_on_missing_event_handler(): void
    {
        $this->shouldThrow(HandlerException::class)
            ->during('fromSubscribers', [[new class implements ApplicationEventSubscriber {
                public static function subscribedTo(): array
                {
                    return [\DateTime::class];
                }
            }]])
        ;
    }

    public function it_maps_event_to_subscriber_method(): void
    {
        $subscriber = new class implements ApplicationEventSubscriber {
            public static function subscribedTo(): array
            {
                return [\DateTime::class];
            }
            public function onDateTime(\DateTime $arg): void {}
        };

        $res = $this->fromSubscribers([$subscriber]);
        $res->shouldHaveCount(1);
        $res->shouldHaveKeyWithValue('DateTime', [[$subscriber, 'onDateTime']]);
    }

    public function it_maps_event_to_multiple_subscribers_method(): void
    {
        $subscriber1 = new class implements ApplicationEventSubscriber {
            public static function subscribedTo(): array
            {
                return [\DateTime::class];
            }
            public function onDateTime1(\DateTime $arg): void {}
        };
        $subscriber2 = new class implements ApplicationEventSubscriber {
            public static function subscribedTo(): array
            {
                return [\DateTime::class];
            }
            public function onDateTime2(\DateTime $arg): void {}
        };

        $res = $this->fromSubscribers([$subscriber1, $subscriber2]);
        $res->shouldHaveCount(1);
        $res->shouldHaveKeyWithValue('DateTime', [
            [$subscriber1, 'onDateTime1'],
            [$subscriber2, 'onDateTime2']
        ]);
    }

}
