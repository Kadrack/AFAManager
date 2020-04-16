<?php
// src/Service/PhotoUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploader
{
    private $image;

    private $image_info;

    private $salt;

    private $targetDirectory;

    private $height;

    private $width;

    public function __construct($targetDirectory, $salt)
    {
        $this->salt = $salt;

        $this->targetDirectory = $targetDirectory;

        $this->height = 110;

        $this->width = 90;
    }

    public function upload(UploadedFile $file, string $old = "", int $compression = 75)
    {
        $extension = $file->guessExtension();

        $fileName = md5(microtime() . $this->salt).'.'.$extension;

        $file->move($this->targetDirectory.'/original', $fileName);

        $this->image_info = getimagesize($this->targetDirectory.'/original/'.$fileName);

        if( $this->image_info[2] == IMAGETYPE_JPEG )
        {
            $this->image = imagecreatefromjpeg($this->targetDirectory.'/original/'.$fileName);
        }
        elseif( $this->image_info[2] == IMAGETYPE_GIF )
        {
            $this->image = imagecreatefromgif($this->targetDirectory.'/original/'.$fileName);
        }
        elseif( $this->image_info[2] == IMAGETYPE_PNG )
        {
            $this->image = imagecreatefrompng($this->targetDirectory.'/original/'.$fileName);
        }

        $ratio = $this->image_info[0] / $this->image_info[1];

        if ($this->width / $this->height > $ratio)
        {
            $this->width = $this->height * $ratio;
        }
        else
        {
            $this->height = $this->width / $ratio;
        }

        $new_image = imagecreatetruecolor($this->width, $this->height);

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $this->width, $this->height, $this->image_info[0], $this->image_info[1]);

        $this->image = $new_image;

        if( $this->image_info[2] == IMAGETYPE_JPEG )
        {
            imagejpeg($this->image, $this->targetDirectory.'/'.$fileName, $compression);
        }
        elseif( $this->image_info[2] == IMAGETYPE_GIF )
        {
            imagegif($this->image, $this->targetDirectory.'/'.$fileName);
        }
        elseif( $this->image_info[2] == IMAGETYPE_PNG )
        {
            imagepng($this->image, $this->targetDirectory.'/'.$fileName);
        }

        if ($old != '')
        {
            unlink($this->targetDirectory.'/'.$old);
            unlink($this->targetDirectory.'/original/'.$old);
        }

        return $fileName;
    }
}
