<?php

namespace App\Container\Helpers;

use Config;

class Compressor {


    private $image;
    private $ratioW;
    private $ratioH;
    private $width;
    private $height;
    private $mime;
    private $name;
    private $folder;

    function __construct($file){
            $this->folder = ".".Config::$files['compressed'];

            list($this->width, $this->height) = getimagesize($file);
            $this->image = $file;
            $this->mime = mime_content_type($file);
            $this->ratioW = $this->height / $this->width;
            $this->ratioH = $this->width / $this->height;
            $this->name = basename($file);

            if(!file_exists($this->folder)){
                if(!mkdir($this->folder, 0777, true)){
                    return ['error' => 'error creating folder '.$this->folder];
                }
            }
    }

    /**
     * static call Compressor($file)->resize...
     * @author Agne *degaard
     * @param  [[Type]] $file [[Description]]
     * @return [[Type]] [[Description]]
     */
    public static function image($file){
        return new Compressor($file);
    }

    public function resizeAuto($size) {
      if($this->width > $this->height) return $this->resizeWidth($size);
      return $this->resizeHeight($size);
    }

    /**
     * resize image by Width
     * @author Agne *degaard
     * @param  integer $w    image width
     * @param  str     $name new name can be null, will use same name
     * @return string  image location
     */
    public function resizeWidth($w, $name = null){
        if($this->width < $w) $w = $this->width;
        $h = $w * $this->ratioW;
        $name = ($name == null) ? $this->name : $name;
        return $this->resize($w, $h, $name);

    }

    /**
     * resize image by Height
     * @author Agne *degaard
     * @param  integer $h    height width
     * @param  str     $name new name can be null, will use same name
     * @return string  image location
     */
    public function resizeHeight($h, $name = null){
        if($this->height < $h) $h = $this->height;
        $w = $h * $this->ratioH;
        $name = ($name == null) ? $this->name : $name;
        return $this->resize($w, $h, $name);
    }

    /**
     * Do the actuall resizeing
     * @author Agne *degaard
     * @param  integer $w
     * @param  integer $h
     * @param  string  $name
     * @return string  image locaton
     */
    public function resize($w, $h, $name){
        $image = imagecreatetruecolor($w, $h);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $folder = $this->folder.intval($w)."x".intval($h)."_".$name;

        switch($this->mime){
            case "image/png":
                imagefill($image,0, 0, 0x7fff0000);
                $source = imagecreatefrompng($this->image);
                break;

            case "image/jpeg":
                $source = imagecreatefromjpeg($this->image);
                break;

            default:
                return "Wrong Image type, only jpeg or png";
                break;

        }

        // Resize
        imagecopyresized($image, $source, 0, 0, 0, 0, $w, $h, $this->width, $this->height);

        switch($this->mime){
            case "image/png":
                imagepng($image, $folder);
                break;

            case "image/jpeg":
                imagejpeg($image, $folder);
                break;

        }

        return  $folder; //'data: '.$this->mime.';base64,'.base64_encode($content);
    }

}
