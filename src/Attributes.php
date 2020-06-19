<?php

namespace Token;

use Hyperf\Utils\Arr;

trait Attributes
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var bool
     */
    protected $isChange = false;

    public function isChange(): bool
    {
        return $this->isChange;
    }

    public function __wakeup()
    {
        $this->isChange = false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::exists($this->attributes, $key);
    }

    /**
     * @param string $key
     * @param null $default
     * @return array|mixed
     */
    public function get(string $key, $default = null)
    {
        return data_get($this->attributes, $key, $default);
    }

    /**
     * @param string $key
     * @return array|\ArrayAccess|mixed
     */
    public function pull(string $key)
    {
        $this->isChange = true;
        return Arr::pull($this->attributes, $key);
    }

    /**
     * Returns attributes.
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void
    {
        $this->isChange = true;
        data_set($this->attributes, $key, $value);
    }

    /**
     * @param array|string $keys
     */
    public function forget($keys): void
    {
        $this->isChange = true;
        Arr::forget($this->attributes, $keys);
    }

    public function clear(): void
    {
        $this->isChange = true;
        $this->attributes = [];
    }
}
