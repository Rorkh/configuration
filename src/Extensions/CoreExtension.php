<?php

namespace Zeantar\Configuration\Extensions;

use Zeantar\Configuration\Extension;
use Zeantar\Configuration\Interface\LoaderInterface;

class CoreExtension extends Extension
{
    public function getLoaders(): array
    {
        return [
            'json' => new class implements LoaderInterface {
                public function getData(string $content): array
                {
                    return json_decode($content, true);
                }
            }
        ];
    }
}