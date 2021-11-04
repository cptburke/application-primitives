<?php


namespace CptBurke\Application\Event;


interface ApplicationEventBus
{

    public function dispatch(ApplicationEvent ...$es): void;

}
