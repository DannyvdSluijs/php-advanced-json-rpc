<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc\Tests;

use StdClass;
use PHPUnit\Framework\TestCase;
use AdvancedJsonRpc\{Dispatcher, Request};

class DispatcherTest extends TestCase
{
    private $calls;
    private $callsOfNestedTarget;
    private $dispatcher;

    public function setUp(): void
    {
        $this->calls = [];
        $this->callsOfNestedTarget = [];
        $target = new Target($this->calls);
        $target->nestedTarget = new Target($this->callsOfNestedTarget);
        $this->dispatcher = new Dispatcher($target);
    }

    public function testCallMethodWithoutArgs()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithoutArgs'));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithoutArgs', [])]);
    }

    public function testCallMethodWithTypeHintWithPositionalArgs()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithTypeHint', [new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }

    public function testCallMethodWithTypeHintWithNamedArgs()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithTypeHint', ['arg' => new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }

    public function testCallMethodWithArrayParamTag()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithArrayParamTag', ['arg' => [new Argument('whatever')]]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithArrayParamTag', [[new Argument('whatever')]])]);
    }

    public function testCallMethodWithMissingArgument()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithDifferentlyTypedArgs', ['arg2' => 0]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithDifferentlyTypedArgs', [0 => null, 1 => 0])]);
    }

    public function testCallMethodWithUnionTypeParamTag()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithUnionTypeParamTag', ['arg' => [new Argument('whatever')]]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithUnionTypeParamTag', [[new Argument('whatever')]])]);
    }
    public function testCallMethodWithTypeHintWithNamedArgsOnNestedTarget()
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'nestedTarget->someMethodWithTypeHint', ['arg' => new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, []);
        $this->assertEquals($this->callsOfNestedTarget, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }

    public function testCallMethodWithArrayTypeHintAndDocblock(): void
    {
        $result = $this->dispatcher->dispatch((string)new Request(1, 'someMethodWithArrayTypeHint', ['args' => [new Argument('1'), new Argument('2')]]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithArrayTypeHint', [[new Argument('1'), new Argument('2')]])]);
    }
  
    public function testCallMethodWithAdditionalProvidedParamsOnSomeMethodWithoutArgs()
    {
        $result = $this->dispatcher->dispatch((string) new Request(1, 'someMethodWithoutArgs', ['arg' => new Argument('whatever')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithoutArgs', [])]);
    }

    public function testCallMethodWithAdditionalProvidedParamsOnSomeMethodWithTypeHint()
    {
        $result = $this->dispatcher->dispatch((string) new Request(1, 'someMethodWithTypeHint', ['arg' => new Argument('whatever'), 'arg2' => new Argument('anything')]));
        $this->assertEquals('Hello World', $result);
        $this->assertEquals($this->calls, [new MethodCall('someMethodWithTypeHint', [new Argument('whatever')])]);
    }
}
