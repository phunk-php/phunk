<?php

namespace Phunk;

abstract class Model implements \IteratorAggregate, \Traversable, \JsonSerializable
{
    public function copy(array $udpatedData): Model
    {
        $class = get_class($this);
        return new $class(...[...$this, ...$udpatedData]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->toArray());
    }

    public static function __set_state(array $data)
    {
        $object = new static();
        return $object->copy($data);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    abstract public function toArray(): array;
}
