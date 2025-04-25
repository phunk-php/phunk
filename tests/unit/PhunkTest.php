<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phunk\Phunk;

class PhunkTest extends TestCase
{
    public function testCompose(): void
    {
        $function1 = function(array $data) {
            $data['test1'] = 'test1';
            return $data;
        };

        $function2 = function(array $data) {
            $data['test2'] = 'test2';
            return $data;
        };

        $initial = ['initial' => 'data'];

        // Run compose
        $result = Phunk::compose([$function1, $function2], $initial);

        $this->assertSame('data', $result['initial']);
        $this->assertSame('test1', $result['test1']);
        $this->assertSame('test2', $result['test2']);
    }

    public function testFn(): void
    {
        $function = function(string $param1, array $data) {
            $data[$param1] = 'new value';
            return $data;
        };

        $initial = ['initial' => 'data'];

        // Run fn
        $result = Phunk::fn($function, 'key', $initial);
        $resultData = $result($initial);

        $this->assertTrue(is_callable($result));
        $this->assertSame('data', $resultData['initial']);
        $this->assertSame('new value', $resultData['key']);
    }

    public function testMap(): void
    {
        $function = function(string $value): string {
            return $value . ' - updated!';
        };

        $initial = ['initial', 'data'];

        // Run map
        $result = Phunk::map($function);
        $resultData = $result($initial);

        $this->assertTrue(is_callable($result));
        $this->assertSame(['initial - updated!', 'data - updated!'], $resultData);
    }

    public function testTry(): void
    {
        $function1 = function(array $data) {
            throw new \Exception('Function1 fails.');
        };

        $function2 = function(array $data) {
            $data['test2'] = 'test2';
            return $data;
        };

        $initial = ['initial' => 'data'];

        // Run compose
        $result = Phunk::try([$function1, $function2], $initial);

        $this->assertSame('data', $result['initial']);
        $this->assertSame('test2', $result['test2']);
        $this->assertFalse(isset($result['test1']));
    }
}
