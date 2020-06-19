<?php

declare(strict_types=1);

namespace TokenTest\Stub;

use Token\Attributes;

class AttributesStub
{
    use Attributes;

    public function __construct(array $attributes=[])
    {
        $this->attributes = $attributes;
    }
}