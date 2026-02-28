<?php
// src/Bundle/UserActivityBundle/UserActivityBundle.php

namespace App\Bundle\UserActivityBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use App\Bundle\UserActivityBundle\DependencyInjection\UserActivityExtension;

class UserActivityBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new UserActivityExtension();
    }
}