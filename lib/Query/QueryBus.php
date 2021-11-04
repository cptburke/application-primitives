<?php


namespace CptBurke\Application\Query;


interface QueryBus
{

    public function ask(Query $q): mixed;

}
