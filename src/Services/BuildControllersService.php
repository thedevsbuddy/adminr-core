<?php

namespace Devsbuddy\AdminrCore\Services;

use Devsbuddy\AdminrCore\Traits\CanManageFiles;
use Devsbuddy\AdminrCore\Traits\HasStubs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BuildControllersService extends LiquidBaseService
{

    protected $apiControllerTargetPath;
    protected $adminControllerTargetPath;

    /**
     * Prepares the service to generate resource
     *
     * @param Request $request
     * @return $this|LiquidBaseService
     */
    public function prepare(Request $request)
    {
        parent::prepare($request);
        $this->apiControllerTargetPath = base_path() . "/app/Http/Controllers/Api/$this->controllerName.php";
        $this->adminControllerTargetPath = base_path() . "/app/Http/Controllers/Admin/$this->controllerName.php";
        return $this;
    }

    /**
     * Build api controller
     *
     * @return $this
     * @throws \Exception
     */
    public function buildApiController()
    {
        try {
            $controllerStub = $this->hasSoftdeletes
                ? $this->getControllerStub('ApiControllerWithSoftdeletes')
                : $this->getControllerStub('ApiController');

            $stubPath = $this->hasSoftdeletes
                ? $this->getControllerStub('ApiControllerWithSoftdeletes', true)
                : $this->getControllerStub('ApiController', true);

            $controllerStub = $this->processStub($controllerStub);

            $this->makeDirectory($this->apiControllerTargetPath);
            File::put($stubPath, $controllerStub);
            File::copy($stubPath, $this->apiControllerTargetPath);

            return $this;
        } catch (\Exception $e){
            throw $e;
        } catch (\Error $e){
            throw $e;
        }

    }

    /**
     * Build admin controller
     *
     * @return $this
     * @throws \Exception
     */
    public function buildController()
    {
        try{
            $controllerStub = $this->hasSoftdeletes
                ? $this->getControllerStub('ControllerWithSoftdeletes')
                : $this->getControllerStub('Controller');

            $stubPath = $this->hasSoftdeletes
                ? $this->getControllerStub('ControllerWithSoftdeletes', true)
                : $this->getControllerStub('Controller', true);

            $controllerStub = $this->processStub($controllerStub);

            $this->makeDirectory($this->adminControllerTargetPath);
            File::put($stubPath, $controllerStub);
            File::copy($stubPath, $this->adminControllerTargetPath);

            return $this;
        } catch (\Exception $e){
            throw $e;
        } catch (\Error $e){
            throw $e;
        }

    }


    /**
     * Processes stubs
     *
     * @param $stub
     * @return mixed
     */

    public function processStub($stub)
    {
        $stub = str_replace('{{MODEL_CLASS}}', $this->modelName, $stub);
        $stub = str_replace('{{CONTROLLER_CLASS}}', $this->controllerName, $stub);
        $stub = str_replace('{{MODEL_ENTITY}}', $this->modelEntity, $stub);
        $stub = str_replace('{{MODEL_ENTITIES}}', $this->modelEntities, $stub);
        $stub = str_replace('{{RESULT_LIMIT}}', config('liquid.api.result_limit') ?: 10, $stub);
        $stub = str_replace('{{SEARCH_STATEMENTS}}', $this->getSearchStatement(), $stub);
        $stub = str_replace('{{VALIDATION_STATEMENT}}', $this->getValidationStatement(), $stub);
        $stub = str_replace('{{UPDATE_VALIDATION_STATEMENT}}', $this->getUpdateValidationStatement(), $stub);
        $stub = str_replace('{{FILE_UPLOAD_STATEMENT}}', $this->getFileUploadStatement(), $stub);
        $stub = str_replace('{{FILE_UPLOAD_API_STATEMENT}}', $this->getFileUploadApiStatement(), $stub);
        $stub = str_replace('{{FILE_UPDATE_STATEMENT}}', $this->getFileUpdateStatement(), $stub);
        $stub = str_replace('{{SAVE_DATA_STATEMENT}}', $this->getSaveDataStatement(), $stub);
        $stub = str_replace('{{UPDATE_DATA_STATEMENT}}', $this->getUpdateDataStatement(), $stub);
        $stub = str_replace('{{DELETE_FILE_STATEMENT}}', $this->getDeleteFileStatement(), $stub);
        $stub = str_replace('{{TRASHED_FILTER}}', $this->getTrashedFilterStatement(), $stub);

        return $stub;
    }

    /**
     * Generate search statement and
     * return statement lines
     */
    protected function getSearchStatement()
    {
        $migrations = $this->request->get('migrations');

        $searchStmt = '';
        foreach ($migrations as $migration) {
            if ($migration['field_name'] != 'id') {
                if ($migration['can_search'] == true) {
                    $searchStmt .= "if(\$request->has('" . Str::snake($migration['field_name']) . "') && !is_null(\$request->get('" . Str::snake($migration['field_name']) . "'))){\n\t\t\t\t";
                    $searchStmt .= "$" . $this->modelEntities . "->where('" . Str::snake($migration['field_name']) . "', 'LIKE', '%'.\$request->get('" . Str::snake($migration['field_name']) . "').'%');\n\t\t\t";
                    $searchStmt .= "}\n\t\t\t";
                }
            }
        }

        return $searchStmt;
    }

    /**
     * Generate trashed filter statement and
     * return statement lines
     */
    protected function getTrashedFilterStatement()
    {
        $trashedFilterStmt = "";
        if($this->hasSoftdeletes){
            $trashedFilterStmt .= "if(\$request->has('trashed') && !is_null(\$request->get('trashed'))){
                \$$this->modelEntities->onlyTrashed();
            }
        ";
        }
        return $trashedFilterStmt;
    }

    /**
     * Generate validation statement and
     * return statement lines
     */
    protected function getValidationStatement()
    {
        $migrations = $this->request->get('migrations');

        $validationStmt = "\$request->validate([\n\t\t\t\t";
        foreach ($migrations as $migration) {
            $lastTabs = ",\n\t\t\t\t";
            if ($migration['data_type'] != 'slug') {
                if ($migration == $migrations[count($migrations) - 1]) {
                    $lastTabs = ",\n\t\t\t";
                }
                $isUnique = "";
                if ($migration['unique']) {
                    $isUnique = ", \"unique:" . $this->tableName . "\"";
                }
                if ($migration['nullable'] == false) {
                    $validationStmt .= "\"" . Str::snake($migration['field_name']) . "\" => [\"required\"" . $isUnique . "]" . $lastTabs . "";
                }
            }
        }
        if($this->hasMedia){
            $validationStmt .= "\t\"" . $this->mediaField . "\" => [\"required\"]\n\t\t\t";
        }
        $validationStmt .= "]);";
        return $validationStmt;
    }

    /**
     * Generate update validation statement and
     * return statement lines
     */
    protected function getUpdateValidationStatement()
    {
        $migrations = $this->request->get('migrations');

        $validationStmt = "\$request->validate([\n\t\t\t\t";
        foreach ($migrations as $migration) {
            $lastTabs = ",\n\t\t\t\t";
            if ($migration['data_type'] != 'slug') {
                if ($migration == $migrations[count($migrations) - 1]) {
                    $lastTabs = "\n\t\t\t";
                }
                if ($migration['nullable'] == false) {
                    $validationStmt .= "\"" . Str::snake($migration['field_name']) . "\" => [\"required\"]" . $lastTabs . "";
                }
            }
        }
        $validationStmt .= "]);";
        return $validationStmt;
    }

    /**
     * Generate File upload statement and
     * return statement lines
     */
    protected function getFileUploadStatement()
    {
        $fileUploadStmt = "";
        if ($this->hasMedia == true) {
            $fileUploadStmt .= "if(\$request->hasFile(\"".$this->mediaField."\")){\n\t\t\t\t";
            $fileUploadStmt .= "\$fileName = \$this->uploadFile(\$request->file(\"".$this->mediaField."\"), \"".$this->modelEntities."\")->getFileName();\n\t\t\t";
            $fileUploadStmt .= "} else {\n\t\t\t\t";
            $fileUploadStmt .= "return back()->with(\"error\", \"Please select an image for media\");\n\t\t\t";
            $fileUploadStmt .= "}\n";
        }
        return $fileUploadStmt;
    }

    /**
     * Generate File upload for API statement and
     * return statement lines
     */
    protected function getFileUploadApiStatement()
    {
        $fileUploadStmt = "";

        if ($this->hasMedia == true) {
            $fileUploadStmt .= "if(\$request->hasFile(\"".$this->mediaField."\")){\n\t\t\t\t";
            $fileUploadStmt .= "\$fileName = \$this->uploadFile(\$request->file(\"".$this->mediaField."\"), \"".$this->modelEntities."\")->getFileName();\n\t\t\t";
            $fileUploadStmt .= "} else {\n\t\t\t\t";
            $fileUploadStmt .= "return \$this->error(\"Please select an image for media\");\n\t\t\t";
            $fileUploadStmt .= "}\n";
        }
        return $fileUploadStmt;

    }


    /**
     * Generate File update statement and
     * return statement lines
     */
    protected function getFileUpdateStatement()
    {
        $fileUploadStmt = "";
        if ($this->hasMedia == true) {
            $fileUploadStmt .= "if(\$request->hasFile(\"".$this->mediaField."\")){\n\t\t\t\t";
            $fileUploadStmt .= "\$fileName = \$this->uploadFile(\$request->file(\"".$this->mediaField."\"), \"".$this->modelEntities."\")->getFileName();\n\t\t\t\t";
            $fileUploadStmt .= "\$this->deleteFileFromStorage($".$this->modelEntity."->".$this->mediaField.");\n\t\t\t";
            $fileUploadStmt .= "} else {\n\t\t\t\t";
            $fileUploadStmt .= "\$fileName = \$".$this->modelEntity."->".$this->mediaField.";\n\t\t\t";
            $fileUploadStmt .= "}\n";
        }
        return $fileUploadStmt;
    }

    /**
     * Generate save data statement and
     * return statement lines
     */
    protected function getSaveDataStatement()
    {
        $migrations = $this->request->get('migrations');

        $saveDataStmt = "";
        foreach ($migrations as $migration) {
            $lastTabs = ",\n\t\t\t\t";
            if ($migration == $migrations[count($migrations) - 1]) {
                if($this->hasMedia == true){
                    $lastTabs = ",\n\t\t\t\t";
                } else {
                    $lastTabs = "";
                }
            }
            if ($migration['field_name'] == 'slug') {
                $saveDataStmt .= "\"" . Str::snake($migration['field_name']) . "\" => Str::slug(\$request->get(\"" . Str::snake($migration['slug_from']) . "\"))" . $lastTabs;
            } else {
                $saveDataStmt .= "\"" . Str::snake($migration['field_name']) . "\" => \$request->get(\"" . Str::snake($migration['field_name']) . "\")" . $lastTabs;
            }
        }

        if ($this->hasMedia == true){
            $saveDataStmt .= "\"" . $this->mediaField . "\" => \$fileName";
        }

        return $saveDataStmt;
    }

    /**
     * Generate update data statement and
     * return statement lines
     */
    protected function getUpdateDataStatement()
    {
        $migrations = $this->request->get('migrations');

        $saveDataStmt = "";
        foreach ($migrations as $migration) {
            $lastTabs = ",\n\t\t\t\t";
            if ($migration['field_name'] != 'id') {
                if ($migration == $migrations[count($migrations) - 1]) {
                    if($this->hasMedia == true){
                        $lastTabs = ",\n\t\t\t\t";
                    } else {
                        $lastTabs = "";
                    }
                }
                if ($migration['field_name'] == 'slug') {
                    $saveDataStmt .= "\"" . Str::snake($migration['field_name']) . "\" => Str::slug(\$request->get(\"" . $migration['slug_from'] . "\"))" . $lastTabs;
                } else {
                    $saveDataStmt .= "\"" . Str::snake($migration['field_name']) . "\" => \$request->get(\"" . Str::snake($migration['field_name']) . "\")" . $lastTabs;
                }
            }
        }
        if ($this->hasMedia == true){
            $saveDataStmt .= "\"" . $this->mediaField . "\" => \$fileName";
        }
        return $saveDataStmt;
    }

    /**
     * Generate delete file statement and
     * return statement lines
     */
    protected function getDeleteFileStatement(){
        $deleteFileStmt = "";
        if ($this->hasMedia == true){
            $deleteFileStmt .= "\$this->deleteFileFromStorage($".$this->modelEntity."->".$this->mediaField.");\n";
        }
        return $deleteFileStmt;
    }

    /**
     * Rollbacks generated files
     *
     * @return $this
     */
    public function rollback()
    {
        if(!is_null($this->controllerName)){
            $this->deleteFile($this->adminControllerTargetPath);
            $this->deleteFile($this->apiControllerTargetPath);
        }
        return $this;
    }
}
