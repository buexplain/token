<?php

declare(strict_types=1);

namespace Token\Handler;

use Token\Contract\TokenHandlerInterface;
use Hyperf\Utils\Filesystem\Filesystem;

class FileHandler implements TokenHandlerInterface
{
    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var string
     */
    private $path;

    public function __construct(Filesystem $files, string $path)
    {
        $this->files = $files;
        $this->path = $path;
        if (! file_exists($path)) {
            $files->makeDirectory($path, 0755, true);
        }
    }

    public function read($id)
    {
        if ($this->files->isFile($path = $this->path . '/' . $id)) {
            return $this->files->sharedGet($path);
        }
        return '';
    }

    public function write(string $id, string $data, int $expire)
    {
        $this->files->put($this->path . '/' . $id, $data, true);
        return true;
    }

    public function delete($id)
    {
        $this->files->delete($this->path . '/' . $id);
        return true;
    }
}
