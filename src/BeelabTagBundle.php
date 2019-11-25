<?php

namespace Beelab\TagBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BeelabTagBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
