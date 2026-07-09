<?php

namespace Phunk;

abstract class Model implements \IteratorAggregate, \Traversable, \JsonSerializable
{
    public function copy(array $udpatedData): Model
    {
        $class = get_class($this);
        return new $class(...[...$this->rawProperties(), ...$udpatedData]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->rawProperties());
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

    public function toArray(): array
    {
        return $this->rawProperties();
    }

    protected function rawProperties(): array
    {
        $properties = [];
        foreach ((new \ReflectionClass($this))->getProperties() as $prop) {
            $properties[$prop->getName()] = $prop->getValue($this);
        }

        return $properties;
    }

    protected function formatDate(mixed $date): ?string
    {
        $date = $this->buildDate($date);
        if ($date instanceof \DateTimeImmutable) {
            return $date->format('c');
        }

        return $date;
    }

    protected function buildDate(mixed $date): ?\DateTimeImmutable
    {
        if ($date === null) {
            return null;
        }

        if (is_string($date)) {
            $date = new \DateTimeImmutable($date);
        }

        return $date;
    }
}
