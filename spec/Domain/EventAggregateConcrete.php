<?php


namespace spec\CptBurke\Application\Domain;


use CptBurke\Application\Domain\DomainEvent;
use CptBurke\Application\Domain\EventAggregate;


class EventAggregateConcrete
{

    use EventAggregate;

    public function raiseTestEvent(): void
    {
        $this->raise(new class implements DomainEvent{});
    }

}
