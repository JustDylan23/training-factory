<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const TRAINING_IMAGE = 'training_image';

    private string $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadArticleImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath . '/' . self::TRAINING_IMAGE;
        $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFilename
        );
        return $newFilename;
    }

    public function getPublicPath(string $path)
    {
        return 'uploads/' . $path;
    }
}