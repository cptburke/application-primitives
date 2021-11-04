<?php


namespace CptBurke\Application\Domain;


interface DomainEventBus
{

    public function dispatch(DomainEvent ...$es): void;

}
