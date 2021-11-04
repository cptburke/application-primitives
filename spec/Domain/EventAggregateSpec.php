<?php


namespace spec\CptBurke\Application\Domain;


use CptBurke\Application\Domain\EventAggregate;
use PhpSpec\ObjectBehavior;


class EventAggregateSpec extends ObjectBehavior
{

    public function let(): void
    {
        $this->beAnInstanceOf(EventAggregateConcrete::class);
    }

    public function it_should_be_empty_on_start(): void
    {
        $this->releaseEvents()->shouldHaveCount(0);
    }

    public function it_should_release_raised_events(): void
    {
        $this->raiseTestEvent();
        $this->releaseEvents()->shouldHaveCount(1);
    }

    public function it_should_clear_released_events(): void
    {
        $this->raiseTestEvent();
        $this->releaseEvents();
        $this->releaseEvents()->shouldHaveCount(0);
    }

}
