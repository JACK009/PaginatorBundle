<?php

namespace Jack009\PaginatorBundle;

use Jack009\PaginatorBundle\DependencyInjection\PaginatorExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PaginatorBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new PaginatorExtension();
        }

        return $this->extension;
    }
}
