<?php

namespace App\Twig;

use App\Services\UploaderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [AppRuntime::class, 'getUploadedAssetPath'])
        ];
    }
}