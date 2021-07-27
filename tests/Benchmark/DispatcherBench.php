<?php

declare(strict_types=1);

namespace AdvancedJsonRpc\Tests\Benchmark;

use AdvancedJsonRpc\Dispatcher;
use AdvancedJsonRpc\Request;
use AdvancedJsonRpc\Tests\Argument;
use AdvancedJsonRpc\Tests\Target;

class DispatcherBench
{
    private $calls;
    private $callsOfNestedTarget;
    private $dispatcher;

    public function __construct()
    {
        $this->calls = [];
        $this->callsOfNestedTarget = [];
        $target = new Target($this->calls);
        $target->nestedTarget = new Target($this->callsOfNestedTarget);
        $this->dispatcher = new Dispatcher($target);
    }

    /**
     * @Revs(5000)
     * @Iterations(5)
     */
    public function benchDispatch(): void
    {
        $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithTypeHint', [new Argument('whatever')]));
    }
}
