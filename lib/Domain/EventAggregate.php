<?php


namespace CptBurke\Application\Domain;


trait EventAggregate
{

    /**
     * @var DomainEvent[]
     */
    protected array $pendingEvents = [];

    protected function raise(DomainEvent $event): void
    {
        $this->pendingEvents[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];

        return $events;
    }

}
