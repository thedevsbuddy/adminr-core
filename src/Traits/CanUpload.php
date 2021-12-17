<?php

namespace Devsbuddy\AdminrCore\Traits;


use Illuminate\Support\Str;

trait CanUpload {
    public $file;
    public $dir;
    public $saveFileName;
    public $uploadedFileName;
    public $uploadedFileNames;
    public $destination;
    public $editableInstance;
    public $mediaInstance;

    /**
     * Uploads the file in storage
     * and also create a record in media table
     *
     * @param mixed $file
     * @param mixed $dir
     * @return $this
     */
    public function uploadFile($file, $dir = null) {
        $this->file = $file;
        $this->dir = $dir;

        $fileName = Str::random(64);
        $fileExtension = $this->file->getClientOriginalExtension();
        $uploadsHome = "uploads/";

        $this->saveFileName = $fileName. "." . strtolower($fileExtension);
        $this->dir = $this->dir ? $uploadsHome.$this->dir."/" : $uploadsHome."/";
        $this->destination = storage_path() . '/app/public/' . $this->dir;
        $this->uploadedFileName = 'storage/' . $this->dir . $this->saveFileName;

        $this->file->move($this->destination, $this->saveFileName);

        return $this;
    }


    /**
     * Uploads the file in storage
     * and also create a record in media table
     *
     * @param array $files
     * @param mixed $dir
     * @return $this
     */
    public function uploadMultipleFile($files, $dir = null) {
        $this->dir = $dir;

        foreach ($files as $file){
            $fileName = Str::random(64);
            $fileExtension = $file->getClientOriginalExtension();
            $uploadsHome = "uploads/media/";

            $this->saveFileName = $fileName. "." . strtolower($fileExtension);
            $this->dir = $this->dir ? $uploadsHome.$this->dir."/" : $uploadsHome."/";
            $this->destination = storage_path() . '/app/public/' . $this->dir;
            $this->uploadedFileName[] = 'storage/' . $this->dir . $this->saveFileName;

            $file->move($this->destination, $this->saveFileName);
        }

        return $this;
    }


    /**
     * Returns the uploaded
     * media file name
     *
     * @return string
     */
    public function getUploadedFileName() {
        return $this->uploadedFileName;
    }


    /**
     * Returns the uploaded
     * media files name
     *
     * @return array
     */
    public function getUploadedFileNames() {
        return $this->uploadedFileNames;
    }
}
