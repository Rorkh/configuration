<?php

namespace Zeantar\Configuration\Interface;

interface LoaderInterface
{
    public function getData(string $content): array;
}