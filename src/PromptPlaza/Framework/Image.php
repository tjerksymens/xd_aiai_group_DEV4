<?php

namespace PromptPlaza\Framework;

use Cloudinary\Cloudinary;

class Image
{
    private $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function upload(array $file): string
    {
        $name = $file['name'];
        $size = $file['size'];
        $tmpName = $file['tmp_name'];
        $error = $file['error'];

        if ($error !== UPLOAD_ERR_OK) {
            throw new \Exception("Error uploading file: {$error}");
        }

        if ($size > 1000000) {
            throw new \Exception("File size exceeds limit of 1MB.");
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $allowedExts = ["jpg", "jpeg", "png"];

        if (!in_array($ext, $allowedExts)) {
            throw new \Exception("Invalid file type. Only JPG, JPEG, and PNG files are allowed.");
        }

        $newName = uniqid("IMG-", true) . '.' . $ext . $ext;
        $this->cloudinary->uploadApi()->upload(
            $tmpName,
            ["public_id" => $newName]
        );

        return $newName;
    }
}
