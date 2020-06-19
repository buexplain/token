<?php

declare(strict_types=1);

namespace Token\Contract;

interface TokenHandlerInterface
{
    public function delete(string $id);
    public function read(string $id);
    public function write(string $id, string $data, int $expire);
}
