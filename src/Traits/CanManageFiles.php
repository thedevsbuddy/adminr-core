<?php

namespace Devsbuddy\AdminrCore\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait CanManageFiles {
    public $file;
    public $dir;
    public $saveFileName;
    public $uploadedFileName;
    public $uploadedFileNames;
    public $destination;

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
            $uploadsHome = "uploads/";

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



    /**
     * Delete any file from the given path
     *
     * @param mixed $path
     * @return $this
     */
    public function deleteFile($path) {
        if (File::exists($path)){
            File::delete($path);
        }
        return $this;
    }


    /**
     * Delete file from the storage path
     *
     * @param mixed $path
     * @return $this
     */
    public function deleteFileFromStorage($path) {
        if(explode('/', $path)[0] == 'storage'){
            $this->deleteFile(storage_path() . '/app/public/' . Str::replace('storage/', '', $path));
        } else {
            $this->deleteFile(storage_path() . '/app/public/' . $path);
        }
        return $this;
    }

    /**
     * Delete any directory from the given path
     *
     * @param mixed $path
     * @return $this
     */
    public function deleteDir($path) {
        if (File::isDirectory($path)){
            File::deleteDirectory($path);
        }
        return $this;
    }


}
