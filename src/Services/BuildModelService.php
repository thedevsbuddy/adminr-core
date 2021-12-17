<?php

namespace Devsbuddy\AdminrCore\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BuildModelService extends LiquidBaseService
{
    protected $modelTargetPath;

    /**
     * Prepares the service to generate resource
     *
     * @param Request $request
     * @return $this|LiquidBaseService
     */
    public function prepare(Request $request)
    {
        parent::prepare($request);
        $this->modelTargetPath = base_path() . "/app/Models/$this->modelName.php";
        return $this;
    }

    /**
     * Generates Model class
     *
     * @return $this
     * @throws \Exception
     */
    public function buildModel()
    {
        try {
            $modelStub = $this->hasSoftdeletes
                ? $this->getModelStub('ModelWithSoftdeletes')
                : $this->getModelStub('Model');
            $stubPath = $this->hasSoftdeletes
                ? $this->getModelStub('ModelWithSoftdeletes', true)
                : $this->getModelStub('Model', true);

            $modelStub = $this->processStub($modelStub);

            $this->makeDirectory($this->modelTargetPath);
            File::put($stubPath, $modelStub);
            File::copy($stubPath, $this->modelTargetPath);

            return $this;
        } catch (\Exception $e) {
            throw $e;
        } catch (\Error $e) {
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
        $stub = str_replace('{{TABLE_NAME}}', $this->tableName, $stub);
        $stub = str_replace('{{MEDIA_ATTRIBUTE_STATEMENT}}', $this->getMediaAttributeStatement(), $stub);
        return $stub;
    }

    /**
     * Generate search statement and
     * return statement lines
     */
    protected function getMediaAttributeStatement()
    {
        $mediaAttributeStmt = '';

        $mediaAttributeStmt .= "public function get" . Str::studly($this->mediaField) . "Attribute(\$value)\n\t";
        $mediaAttributeStmt .= "{\n\t\t";
        $mediaAttributeStmt .= "if (Str::contains(request()->url(), 'api')){\n\t\t\t";
        $mediaAttributeStmt .= "return asset(\$value);\n\t\t";
        $mediaAttributeStmt .= "}\n\t\t";
        $mediaAttributeStmt .= "return \$value;\n\t";
        $mediaAttributeStmt .= "}\n";

        return $this->hasMedia ? $mediaAttributeStmt : null;
    }


    /**
     * Rollbacks generated files
     *
     * @return $this
     */
    public function rollback()
    {
        if (!is_null($this->modelName)) {
            $this->deleteFile($this->modelTargetPath);
        }
        return $this;
    }
}
