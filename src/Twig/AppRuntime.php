<?php


namespace App\Twig;


use App\Services\UploaderHelper;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    private $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getUploadedAssetPath(string $path): string
    {
        return $this->uploaderHelper->getPublicPath($path);
    }
}