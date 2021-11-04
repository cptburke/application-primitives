# Application Primitives
Collection of useful buildings blocks for applications / services (CQRS, DDD etc.).

# Installation
`composer require cptburke/application-primitives`

# Usage
General ideas for usages, see TODO for implentation.

## Domain
```php
<?php


use CptBurke\Application\Domain\EventAggregate;
use CptBurke\Application\Domain\DomainEvent;
use CptBurke\Application\Domain\DomainEventBus;
use CptBurke\Application\Domain\DomainEventSubscriber;


class DomainSpecificEvent implements DomainEvent
{}

class OtherDomainSpecificEvent implements DomainEvent
{}


class DomainObject
{

    use EventAggregate;

    //...
    
    public function doSomething(): void
    {
        //...
        $this->raise(new DomainSpecificEvent());
    }
    
    //...
    
}

class DomainService implements DomainEventSubscriber
{

    private DomainEventBus $bus;
    
    //...
    
    public static function subscribedTo() : array
    {
        return [ OtherDomainSpecificEvent::class ];
    }
    
    //...
    
    public function doSomethingInDomain(/* ... */): void
    {
        //...
        /** @var DomainObject $obj */
        $obj->doSomething();
        $this->bus->dispatch(...$obj->releaseEvents());
        /*
         * [DomainSpecificEvent]
         */
    }
    
    //...
    
    public function onOtherEvent(OtherDomainSpecificEvent $e): void
    {
        //...
    }

}
    
```

## Application

### Event
```php
<?php


use CptBurke\Application\Event\ApplicationEvent;
use CptBurke\Application\Event\ApplicationEventBus;
use CptBurke\Application\Event\ApplicationEventSubscriber;


class ApplicationSpecificEvent implements ApplicationEvent
{}

class OtherApplicationSpecificEvent implements ApplicationEvent
{}

class ApplicationAction implements ApplicationEventSubscriber
{

    private ApplicationEventBus $bus;
    
    //...
    
    public static function subscribedTo() : array
    {
        return [ OtherApplicationSpecificEvent::class ];
    }
    
    //...
    
    public function doAction(/* ... */): void
    {
        //...
        $this->bus->dispatch(new ApplicationSpecificEvent());
    }
    
    //...
    
    public function onOtherEvent(OtherApplicationSpecificEvent $e): void
    {
        //...
    }

}
    
```
### Command
```php
<?php


use CptBurke\Application\Command\Command;
use CptBurke\Application\Command\CommandBus;
use CptBurke\Application\Command\CommandHandler;


/** should be serializable pojo */
final class ToDo implements Command
{

    /** 
     * public string $foo;
     * public string $bar;
     */

}

class DoSomething implements CommandHandler
{

    //...
    
    public function __invoke(ToDo $command): void
    {
        //...
    }
    
    //...
    
}

class Controller
{

    private CommandBus $bus;
    
    //...
    
    public function action(/* ... */): void
    {
        //...
        $this->bus->dispatch(new ToDo(), [/** extra context, e.g. execution time, ... */]);
        //...
    }
    
    //...

}
    
```
### Query
```php
<?php


use CptBurke\Application\Query\Query;
use CptBurke\Application\Query\QueryBus;
use CptBurke\Application\Query\QueryHandler;


/** should be serializable pojo */
final class Question implements Query
{

    /** 
     * public int $page;
     */

}

/** e.g. database query result as pojo */
final class Answer
{

    /** 
     * public int $page;
     * public array $data;
     */
     
}

/** should be handled synchronous */
class AnswerSomething implements QueryHandler
{

    //...
    
    public function __invoke(Question $command): Answer
    {
        //...
    }
    
    //...
    
}

class Controller
{

    private QueryBus $bus;
    
    //...
    
    public function action(/* ... */): void
    {
        //...
        $answer = $this->bus->ask(new Question());
        //...
    }
    
    //...

}
    
```

# Reflection / Framework Utilities

```php
<?php


use CptBurke\Application\Reflection\CallableExtractor;
use CptBurke\Application\Command\Command;
use CptBurke\Application\Domain\DomainEvent;


$extractor = new CallableExtractor();
$commandMapping = $extractor->fromCallables(
    [Command::class, new CommandHandler()],
);

$eventMapping = $extractor->fromSubscribers(
    [DomainEvent::class, new DomainEventHandler()],
    [DomainEvent::class, new OtherDomainEventHandler()]
)
```
