<?php


namespace CptBurke\Application\Event;


interface ApplicationEventSubscriber
{

    /**
     * @return string[]
     */
    public static function subscribedTo(): array;

}
