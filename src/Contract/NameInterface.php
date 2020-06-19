<?php

declare(strict_types=1);

namespace Token\Contract;

use JsonSerializable;

interface NameInterface extends JsonSerializable
{
    /**
     * 刷新token
     * @return string
     */
    public function refresh(): string;

    /**
     * 获取过期时间
     * @return int
     */
    public function expire(): int;

    public function __toString(): string;

    public function has(string $name): bool;

    public function isChange(): bool;

    /**
     * @param string $name
     * @param null $default
     * @return array|mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     * @return array|\ArrayAccess|mixed
     */
    public function pull(string $name);

    /**
     * Returns attributes.
     */
    public function all(): array;

    /**
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value): void;

    /**
     * @param array|string $keys
     */
    public function forget($keys): void;

    public function clear(): void;
}
