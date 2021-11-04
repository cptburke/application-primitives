<?php


namespace CptBurke\Application\Command;


interface CommandBus
{

    public function dispatch(Command $c, array $context = []): mixed;

}
