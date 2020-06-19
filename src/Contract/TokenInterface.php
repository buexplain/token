<?php

declare(strict_types=1);

namespace Token\Contract;

interface TokenInterface
{
    /**
     * 判断客户端传递的token是否有效
     * @return bool
     */
    public function isValid(): bool;

    /**
     * 设置用户的唯一值
     * 注意该值会发送到客户端，有泄漏风险
     * @param string $id
     * @return mixed
     */
    public function setId(string $id);

    /**
     * 返回当前token的名称
     * @return NameInterface
     */
    public function getName(): NameInterface;

    /**
     * 返回当前token的所有名称
     * @return NameInterface[]
     */
    public function getNames(): array;

    public function load(): bool;

    public function save();

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

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

    /**
     * 销毁全部数据
     */
    public function destroyAll(): void;

    /**
     * 销毁当前name的数据
     */
    public function destroySelf(): void;

    /**
     * 销毁其它name的数据
     */
    public function destroyOther(): void;

    /**
     * 销毁指定name
     * @param NameInterface $name
     */
    public function destroyByName(NameInterface $name): void;
}
