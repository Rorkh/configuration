<?php

namespace Zeantar\Configuration;

abstract class Extension
{
    public function getLoaders(): array
    {
        return [];
    }
}