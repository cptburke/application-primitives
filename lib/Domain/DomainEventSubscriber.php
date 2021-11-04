<?php


namespace CptBurke\Application\Domain;


interface DomainEventSubscriber
{

    /**
     * @return string[]
     */
    public static function subscribedTo(): array;

}
