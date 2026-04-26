<?php

namespace Phunk;

abstract class Result
{
    public function __construct(protected mixed $value = null)
    {
    }

    /** Factory for Ok */
    public static function ok(mixed $value = null): Result
    {
        return new Ok($value);
    }

    /** Factory for Err */
    public static function err(mixed $error = null): Result
    {
        return new Err($error);
    }

    /** Whether this is Ok */
    public function isOk(): bool
    {
        return $this instanceof Ok;
    }

    /** Whether this is Err */
    public function isErr(): bool
    {
        return $this instanceof Err;
    }

    /**
     * Get the inner value or throw if Err.
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function unwrap(): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }
        throw new \RuntimeException('Called unwrap on Err: ' . var_export($this->value, true));
    }

    /**
     * Get the inner Error or throw if Ok.
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function unwrapErr(): mixed
    {
        if ($this->isErr()) {
            return $this->value;
        }
        throw new \RuntimeException('Called unwrapErr on Ok: ' . var_export($this->value, true));
    }

    /**
     * Return the value or a default if Err.
     *
     * @template U
     * @param U $default
     * @return T|U
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $this->isOk() ? $this->value : $default;
    }

    /**
     * Map the Ok value with $fn; preserves Err.
     *
     * @template U
     * @param callable(T):U $fn
     * @return Result<U, E>
     */
    public function map(callable $fn, ...$params): Result
    {
        if ($this->isOk()) {
            array_unshift($params, $this->value);  // value prepended FIRST
            $res = call_user_func_array($fn, $params);

            return self::ok($res);
        }
        return self::err($this->value);
    }

    /**
     * Map the Err value with $fn; preserves Ok.
     *
     * @template F
     * @param callable(E):F $fn
     * @return Result<T, F>
     */
    public function mapErr(callable $fn): Result
    {
        if ($this->isErr()) {
            return self::err($fn($this->value));
        }
        return self::ok($this->value);
    }

    /**
     * Chain another operation that returns Result; only runs on Ok.
     *
     * @template U
     * @param callable(T):Result<U, E> $fn
     * @return Result<U, E>
     */
    public function andThen(callable $fn, ...$params): Result
    {
        if ($this->isOk()) {
            array_unshift($params, $this->value);  // value prepended FIRST
            $res = call_user_func_array($fn, $params);

            if (! ($res instanceof Result)) {
                throw new \RuntimeException('andThen callback must return a Result, got ');
            }

            return $res;
        }

        return self::err($this->value);
    }

    /**
     * Chain another operation that returns Result; only runs on Ok.
     *
     * @template U
     * @param callable(T):Result<U, E> $fn
     * @return Result<U, E>
     */
    public function orElse(callable $fn, ...$params): Result
    {
        if ($this->isErr()) {
            array_unshift($params, $this->value);  // value prepended FIRST
            $res = call_user_func_array($fn, $params);

            if (! ($res instanceof Result)) {
                throw new \RuntimeException('orElse callback must return a Result');
            }

            return $res;
        }

        return self::ok($this->value);
    }

    /**
     * Match-style handler: provide two handlers and get a value back.
     *
     * @template R
     * @param callable(T):R $onOk
     * @param callable(E):R $onErr
     * @return R
     */
    public function match(callable $onOk, callable $onErr): mixed
    {
        return $this->isOk() ? $onOk($this->value) : $onErr($this->value);
    }

    /**
     * Inspect the inner value (for debugging) without changing it.
     *
     * @param callable(mixed):void $fn
     * @return Result<T,E> $this
     */
    public function inspect(callable $fn, ...$params): Result
    {
        array_unshift($params, $this->value);  // value prepended FIRST
        call_user_func_array($fn, $params);
 
        return $this;
    }
}