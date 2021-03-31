<?php
// src/Service/PhotoUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class PhotoUploader
 * @package App\Service
 */
class PhotoUploader
{
    /**
     * @var
     */
    private $image;

    /**
     * @var string
     */
    private string $salt;

    /**
     * @var string
     */
    private string $targetDirectory;

    /**
     * @var int
     */
    private int $height;

    /**
     * @var int
     */
    private int $width;

    /**
     * PhotoUploader constructor.
     * @param $targetDirectory
     * @param $salt
     */
    public function __construct($targetDirectory, $salt)
    {
        $this->salt = $salt;

        $this->targetDirectory = $targetDirectory;

        $this->height = 110;

        $this->width = 90;
    }

    /**
     * @param UploadedFile $file
     * @param string $old
     * @param int $compression
     * @return string
     */
    public function upload(UploadedFile $file, string $old = "", int $compression = 75): string
    {
        $extension = $file->guessExtension();

        $fileName = md5(microtime() . $this->salt).'.'.$extension;

        $file->move($this->targetDirectory.'/original', $fileName);

        $image_info = getimagesize($this->targetDirectory.'/original/'.$fileName);

        if( $image_info[2] == IMAGETYPE_JPEG )
        {
            $this->image = imagecreatefromjpeg($this->targetDirectory.'/original/'.$fileName);
        }
        elseif( $image_info[2] == IMAGETYPE_GIF )
        {
            $this->image = imagecreatefromgif($this->targetDirectory.'/original/'.$fileName);
        }
        elseif( $image_info[2] == IMAGETYPE_PNG )
        {
            $this->image = imagecreatefrompng($this->targetDirectory.'/original/'.$fileName);
        }

        $ratio = $image_info[0] / $image_info[1];

        if ($this->width / $this->height > $ratio)
        {
            $this->width = $this->height * $ratio;
        }
        else
        {
            $this->height = $this->width / $ratio;
        }

        $new_image = imagecreatetruecolor($this->width, $this->height);

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $this->width, $this->height, $image_info[0], $image_info[1]);

        $this->image = $new_image;

        if( $image_info[2] == IMAGETYPE_JPEG )
        {
            imagejpeg($this->image, $this->targetDirectory.'/'.$fileName, $compression);
        }
        elseif( $image_info[2] == IMAGETYPE_GIF )
        {
            imagegif($this->image, $this->targetDirectory.'/'.$fileName);
        }
        elseif( $image_info[2] == IMAGETYPE_PNG )
        {
            imagepng($this->image, $this->targetDirectory.'/'.$fileName);
        }

        $this->delete($old);

        return $fileName;
    }

    /**
     * @param string|null $photo
     * @return bool|string
     */
    public function delete(?string $photo): bool|string
    {
        if ((file_exists($this->targetDirectory.'/'.$photo)) && ($photo != null))
        {
            unlink($this->targetDirectory.'/'.$photo);
        }

        if ((file_exists($this->targetDirectory.'/original/'.$photo)) && ($photo != null))
        {
            unlink($this->targetDirectory.'/original/'.$photo);
        }

        return true;
    }
}
