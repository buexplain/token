<?php

declare(strict_types=1);

namespace Token;

use Token\Contract\NameInterface;
use Token\Contract\TokenInterface;
use Hyperf\Utils\Context;

class TokenProxy implements TokenInterface
{
    protected function getToken(): TokenInterface
    {
        return Context::get(TokenInterface::class);
    }

    public function isValid(): bool
    {
        return $this->getToken()->isValid();
    }

    public function setId(string $id)
    {
        $this->getToken()->setId($id);
    }

    public function getName(): NameInterface
    {
        return self::getToken()->getName();
    }

    /**
     * @return NameInterface[]
     */
    public function getNames(): array
    {
        return self::getToken()->getNames();
    }

    public function load(): bool
    {
        return $this->getToken()->load();
    }

    public function save()
    {
        $this->getToken()->save();
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return $this->getToken()->has($name);
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, $default = null)
    {
        return $this->getToken()->get($name, $default);
    }

    /**
     * @inheritDoc
     */
    public function pull(string $name)
    {
        return $this->getToken()->pull($name);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->getToken()->all();
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, $value): void
    {
        $this->getToken()->set($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function forget($keys): void
    {
        $this->getToken()->forget($keys);
    }

    public function clear(): void
    {
        $this->getToken()->clear();
    }

    public function destroyAll(): void
    {
        $this->getToken()->destroyAll();
    }

    public function destroySelf(): void
    {
        $this->getToken()->destroySelf();
    }

    public function destroyOther(): void
    {
        $this->getToken()->destroyOther();
    }

    public function destroyByName(NameInterface $name): void
    {
        $this->getToken()->destroyByName($name);
    }
}
