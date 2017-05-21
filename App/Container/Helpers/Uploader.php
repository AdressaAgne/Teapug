<?php

namespace App\Container\Helpers;

use Compressor, DB, Config;
// Image Uploader

class Uploader {

    private $allowed_files = ["jpg", "jpeg", "png"];
    private $filename;

    private $tmp_path;
    private $folder;//Spør Agne om folder
    private $size;
    private $type = null;
    private $filepath;
    
    public $errors = [];
    public $upload_errors_array = [
      UPLOAD_ERR_OK           => "There is no error",
      UPLOAD_ERR_INI_SIZE     => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
      UPLOAD_ERR_FORM_SIZE    => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
      UPLOAD_ERR_PARTIAL      => "The uploaded file was only partially uploaded.",
      UPLOAD_ERR_NO_FILE      => "No file was uploaded.",
      UPLOAD_ERR_NO_TMP_DIR   => "Missing a temporary folder.",
      UPLOAD_ERR_CANT_WRITE   => "Failed to write file to disk.",
      UPLOAD_ERR_EXTENSION    => "A PHP extension stopped the file upload."
    ];

    public function __construct($file){
        $this->folder = ".".Config::$files['original'];
        $this->filepath = $file;
    }//Contstruct()
    
    public function upload(){
        $file = $this->filepath;
                

        if(!file_exists($this->folder)){
            if(!mkdir($this->folder, 0777, true)){
                return ['error' => 'error creating folder '.$this->folder];
            }
        }

        if(empty($file) || !$file || !is_array($file)) {
          $this->errors[] = "There was no file uploaded here";
          return $this->errors;
        }

        if($file['error'] != 0) {
          $this->errors[] = $this->upload_errors_array[$file['error']];
          return $this->errors;
        }
        
        
        
        $this->tmp_path = $file['tmp_name'];
        $this->size     = $file['size'];
        $this->filename =  strtolower(uniqid().".".explode('/', $file['type'])[1]);
        
        foreach($this->allowed_files as $type){
          if(strtolower($file["type"]) == strtolower('image/'.$type)){
            $this->type = $file['type'];
          }
        }

        if($this->type == null) return $file['type'] . " is not allowed";
        
        try{
            if(move_uploaded_file($file['tmp_name'], $this->picture_path())) {
                $compressSize = Config::$files['compressedSize'];
                $compressSize2 = Config::$files['compressedSize2'];
                
                $small = Compressor::image($this->picture_path())->resizeAuto($compressSize);
                $big = Compressor::image($this->picture_path())->resizeAuto($compressSize2);
                
                $big = trim($big, '.');
                $small = trim($small, '.');
                unlink($this->picture_path());
                
                $id = (isset($_SESSION['uuid']) ? $_SESSION['uuid'] : 0);
                
                $id = DB::insert('image', [[
                    'user_id' => $id,
                    'small' => $small,
                    'big' => $big,
                ]]);

              return ['id' => $id, 'folder' => $big];
            }
        } catch (Exception $e) {
             return ['error' => $e];   
        }
       return ['error' => 'Something went wrong', 'file' => $file];
    }

    public function picture_path() {
      return $this->folder.$this->filename;
    }//picture_path()

}
