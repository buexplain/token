<?php

namespace Token;

use Token\Contract\NameInterface;
use Hyperf\Utils\Str;

class Name implements NameInterface
{
    use Attributes;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var int
     */
    protected $expireTime;

    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $length = 32;

    public function __construct(string $id, int $expire, $length=32)
    {
        $this->id = $id;
        $this->length = $length;
        $this->expire = $expire;
        $this->refresh();
    }

    public function refresh(): string
    {
        $this->name = Str::random($this->length).$this->id;
        $this->expireTime = $this->expire + time();
        $this->isChange = true;
        return $this->name;
    }

    public function expire(): int
    {
        return $this->expireTime;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return $this->name;
    }
}
