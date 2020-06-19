<?php

declare(strict_types=1);

namespace Token;

use Token\Contract\NameInterface;
use Token\Contract\TokenHandlerInterface;
use Hyperf\Utils\Str;
use Token\Contract\TokenInterface;

/**
 * Class Token
 * @package Token
 */
class Token implements TokenInterface
{
    use Attributes;

    /**
     * @var NameInterface[]
     */
    protected $names = [];

    /**
     * @var TokenHandlerInterface
     */
    protected $handler;

    /**
     * @var
     */
    protected $id;

    /**
     * @var bool
     */
    protected $isValid = false;

    /**
     * @var bool
     */
    protected $isDestroy = false;

    /**
     * @var NameInterface
     */
    protected $currentName;

    /**
     * @var string
     */
    protected $clientName = '';

    /**
     * @var int
     */
    protected $expire = 1200;

    /**
     * @var int
     */
    protected $length = 32;

    public function __construct(TokenHandlerInterface $handler, int $expire=1200, $length=32, $name=null)
    {
        $this->handler = $handler;
        $this->expire = $expire;
        $this->length = $length;
        if (is_string($name) && ctype_alnum($name) && strlen($name) > $this->length) {
            $this->id = substr($name, $this->length);
            $this->clientName = $name;
        }
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isChange(): bool
    {
        foreach ($this->names as $name) {
            if ($name->isChange()) {
                $this->isChange = true;
                break;
            }
        }
        return $this->isChange;
    }

    public function setId(string $id)
    {
        if (!ctype_alnum($id)) {
            $id = str_replace(['/', '+', '='], ['g', 'h', 'm'], base64_encode($id));
        }
        $this->id = $id;
    }

    public function getName(): NameInterface
    {
        if (empty($this->currentName)) {
            if (is_null($this->id)) {
                $this->id = Str::random($this->length);
            }
            $this->currentName = new Name($this->id, $this->expire, $this->length);
            $this->names[] = $this->currentName;
            $this->isChange = true;
        }
        return $this->currentName;
    }

    /**
     * @return NameInterface[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function load(): bool
    {
        if (is_null($this->id) || strlen($this->id) == 0) {
            return false;
        }
        if ($data = $this->handler->read($this->id)) {
            $data = @unserialize($data);
            if ($data !== false && ! is_null($data) && is_array($data)) {
                $names = [];
                //踢掉过期的token
                if (isset($data['_n'])) {
                    $currentTime = time();
                    foreach ($data['_n'] as $name) {
                        /**
                         * @var $name NameInterface
                         */
                        if ($name->expire() < $currentTime) {
                            continue;
                        }
                        $names[] = $name;
                    }
                    unset($data['_n']);
                }

                //检查客户端传递的token是否有效
                if (!empty($this->clientName)) {
                    $hasCurrentName = false;
                    foreach ($names as $name) {
                        if ((string) $name == $this->clientName) {
                            $this->currentName = $name;
                            $this->isValid = true;
                            $hasCurrentName = true;
                            break;
                        }
                    }
                    if (!$hasCurrentName) {
                        $this->currentName = null;
                        $this->isValid = false;
                        return false;
                    }
                }

                $this->names = $names;
                $this->attributes = $data;
                return true;
            }
        }
        return false;
    }

    public function save()
    {
        if (is_null($this->id) || strlen($this->id) == 0) {
            return;
        }
        if ($this->isDestroy) {
            $this->handler->delete($this->id);
        } elseif ($this->isChange()) {
            $this->attributes['_n'] = $this->names;
            $this->handler->write($this->id, serialize($this->attributes), $this->getName()->expire() - time());
        }
    }

    public function destroyAll(): void
    {
        $this->isDestroy = true;
        $this->names = [];
        $this->attributes = [];
    }

    public function destroySelf(): void
    {
        if (empty($this->currentName)) {
            return;
        }
        foreach ($this->names as $key=>$name) {
            if (((string) $name) ==  ((string) $this->currentName)) {
                unset($this->names[$key]);
                $this->isChange = true;
                break;
            }
        }
    }

    public function destroyOther(): void
    {
        if (empty($this->currentName)) {
            return;
        }
        foreach ($this->names as $key=>$name) {
            if (((string) $name) ==  ((string) $this->currentName)) {
                $this->isChange = true;
                $this->names = [$this->currentName];
                break;
            }
        }
    }

    public function destroyByName(NameInterface $name): void
    {
        foreach ($this->names as $key=>$n) {
            if (((string) $name) ==  ((string) $n)) {
                unset($this->names[$key]);
                $this->isChange = true;
                break;
            }
        }
    }
}
