<?php

namespace Newride\swagger;

class RelativePath
{
    public $basePath;
    public $pathInfo;

    public function __construct(string $basePath, string $pathInfo)
    {
        $this->basePath = $basePath;
        $this->pathInfo = $pathInfo;
    }

    public function getRelativePath(): string
    {
        if (starts_with($this->pathInfo, $this->basePath)) {
            return substr($this->pathInfo, strlen($this->basePath));
        }

        return $this->pathInfo;
    }

    public function __toString(): string
    {
        return $this->getRelativePath();
    }
}
