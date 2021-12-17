<?php

namespace Devsbuddy\AdminrCore\Services;

use Devsbuddy\AdminrCore\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BuildViewsService extends LiquidBaseService
{
    protected $viewIndexTargetPath;
    protected $viewCreateTargetPath;
    protected $viewEditTargetPath;


    /**
     * Prepares the service to generate views
     *
     * @param Request $request
     * @return $this|LiquidBaseService
     */
    public function prepare(Request $request)
    {
        parent::prepare($request);
        $this->viewIndexTargetPath = base_path() . "/resources/views/admin/$this->modelEntities/index.blade.php";
        $this->viewCreateTargetPath = base_path() . "/resources/views/admin/$this->modelEntities/create.blade.php";
        $this->viewEditTargetPath = base_path() . "/resources/views/admin/$this->modelEntities/edit.blade.php";
        return $this;
    }


    /**
     * Generates index view
     *
     * @return $this
     * @throws \Exception
     */
    public function buildIndexView()
    {
        try {
            $indexStub = $this->getViewStub('index');
            $stubPath = $this->getViewStub('index', true);
            $indexStub = $this->processStub($indexStub);

            $this->makeDirectory($this->viewIndexTargetPath);
            File::put($stubPath, $indexStub);
            File::copy($stubPath, $this->viewIndexTargetPath);

            return $this;
        } catch (\Exception $e) {
            throw $e;
        } catch (\Error $e) {
            throw $e;
        }
    }

    /**
     * Generates create view
     *
     * @return $this
     * @throws \Exception
     */
    public function buildCreateView()
    {
        try {
            $createStub = $this->getViewStub('create');
            $stubPath = $this->getViewStub('create', true);
            $createStub = $this->processStub($createStub);

            $this->makeDirectory($this->viewCreateTargetPath);
            File::put($stubPath, $createStub);
            File::copy($stubPath, $this->viewCreateTargetPath);

            return $this;
        } catch (\Exception $e) {
            throw $e;
        } catch (\Error $e) {
            throw $e;
        }
    }

    /**
     * Generates edit view
     *
     * @return $this
     * @throws \Exception
     */
    public function buildEditView()
    {
        try {
            $editView = $this->getViewStub('edit');
            $stubPath = $this->getViewStub('edit', true);
            $editView = $this->processStub($editView);

            $this->makeDirectory($this->viewEditTargetPath);
            File::put($stubPath, $editView);
            File::copy($stubPath, $this->viewEditTargetPath);

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
        $stub = str_replace('{{FORM_STATEMENT}}', $this->getFormStatement(), $stub);
        $stub = str_replace('{{FORM_EDIT_STATEMENT}}', $this->getEditFormStatement(), $stub);
        $stub = str_replace('{{MODEL_ENTITY}}', $this->modelEntity, $stub);
        $stub = str_replace('{{MODEL_ENTITIES}}', $this->modelEntities, $stub);
        $stub = str_replace('{{LIST_TABLE_HEAD_STATEMENT}}', $this->getListTableHeadStatement(), $stub);
        $stub = str_replace('{{ENTITIES_LIST_STATEMENT}}', $this->getEntitiesListStatement(), $stub);
        $stub = str_replace('{{EMPTY_DATA_STATEMENT}}', $this->getEmptyDataStatement(), $stub);
        $stub = str_replace('{{CKEDITOR_STATEMENT}}', $this->getCkeditorStatement(), $stub);
        $stub = str_replace('{{IMAGE_UPLOAD_STATEMENT}}', $this->getImageUploadStatement(), $stub);
        $stub = str_replace('{{IMAGE_UPDATE_STATEMENT}}', $this->getImageUpdateStatement(), $stub);
        $stub = str_replace('{{TRASHED_BUTTONS}}', $this->getTrashedButtonsStatement(), $stub);

        return $stub;
    }


    /**
     * Generate form for create
     *
     * @return string
     */
    private function getFormStatement()
    {
        $migrations = $this->request->get('migrations');

        $formStmt = "<div class=\"row\">\n";
        foreach ($migrations as $migration) {
            if ($migration['show_form']) {
                $formStmt .= $this->getInputField($migration) . "\n";
            }
        }
        $formStmt .= "\t\t\t\t\t\t</div>\n";

        return $formStmt;
    }

    /**
     * Generate form for edit
     *
     * @return string
     */
    private function getEditFormStatement()
    {
        $migrations = $this->request->get('migrations');

        $formStmt = "<div class=\"row\">\n";
        foreach ($migrations as $migration) {
            if ($migration['show_form']) {
                $formStmt .= $this->getEditInputField($migration) . "\n";
            }
        }
        $formStmt .= "\t\t\t\t\t\t</div>\n";

        return $formStmt;
    }

    /**
     * Generates input fields for create
     *
     * @param $migration
     * @return mixed
     */
    private function getInputField($migration)
    {
        $isNumeric = in_array($migration['data_type'], Database::numericTypes());
        $isInteger = in_array($migration['data_type'], Database::integerTypes());
        $isIncrement = in_array($migration['data_type'], Database::incrementTypes());
        $isTime = in_array($migration['data_type'], Database::timeTypes());
        $isLongText = in_array($migration['data_type'], Database::longTextDataTypes());

        if ($isNumeric) {
            $numberInput = $this->getViewStub('number-input');
            return $this->processInputStubs($numberInput, $migration);
        } else if ($isInteger) {
            $numberInput = $this->getViewStub('number-input');
            return $this->processInputStubs($numberInput, $migration);
        } else if ($isIncrement) {
            // We will not have this type
            // as it is primary key and auto generated
        } else if ($isTime) {

        } else if ($isLongText) {
            $longTextInput = $this->getViewStub('text-input');
            return $this->processInputStubs($longTextInput, $migration);
        } else {
            $stringInput = $this->getViewStub('string-input');
            return $this->processInputStubs($stringInput, $migration);
        }
    }

    /**
     * Generates input fields for edit
     *
     * @param $migration
     * @return mixed
     */
    private function getEditInputField($migration)
    {
        $isNumeric = in_array($migration['data_type'], Database::numericTypes());
        $isInteger = in_array($migration['data_type'], Database::integerTypes());
        $isIncrement = in_array($migration['data_type'], Database::incrementTypes());
        $isTime = in_array($migration['data_type'], Database::timeTypes());
        $isLongText = in_array($migration['data_type'], Database::longTextDataTypes());

        if ($isNumeric) {
            $numberInput = $this->getViewStub('number-edit-input');
            return $this->processInputStubs($numberInput, $migration);
        } else if ($isInteger) {
            $numberInput = $this->getViewStub('number-edit-input');
            return $this->processInputStubs($numberInput, $migration);
        } else if ($isIncrement) {
            // We will not have this type
            // as it is primary key and auto generated
        } else if ($isTime) {

        } else if ($isLongText) {
            $longTextInput = $this->getViewStub('text-edit-input');
            return $this->processInputStubs($longTextInput, $migration);
        } else {
            $stringInput = $this->getViewStub('string-edit-input');
            return $this->processInputStubs($stringInput, $migration);
        }
    }

    /**
     * Processes input stubs
     *
     * @param $stub
     * @param $migration
     * @return mixed
     */
    private function processInputStubs($stub, $migration)
    {
        $stub = str_replace('{{MODEL_ENTITY}}', $this->modelEntity, $stub);
        $stub = str_replace('{{FIELD_NAME}}', Str::snake($migration['field_name']), $stub);
        $stub = str_replace('{{FIELD_NAME_LABEL}}', Str::studly($migration['field_name']), $stub);
        $stub = str_replace('{{FIELD_NAME_LABEL}}', Str::studly($migration['field_name']), $stub);
        $stub = str_replace('{{COL_SM}}', "col-sm-" . $migration['col_sm'], $stub);
        $stub = str_replace('{{COL_MD}}', "col-md-" . $migration['col_md'], $stub);
        $stub = str_replace('{{COL_LG}}', "col-lg-" . $migration['col_lg'], $stub);
        if ($migration['is_rich_text']) {
            $stub = str_replace('{{CKEDITOR_CLASS}}', "ckeditor", $stub);
        } else {
            $stub = str_replace('{{CKEDITOR_CLASS}}', "", $stub);
        }

        return $stub;
    }


    /**
     * Generates CKEDITOR for rich text type field
     *
     * @return string
     */
    private function getCkeditorStatement()
    {
        $migrations = $this->request->get('migrations');

        $ckeditorStmt = "";
        foreach ($migrations as $migration) {
            if ($migration['is_rich_text']) {
                $ckeditorStmt = "<script src=\"https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js\"></script>
    <script>
        CKEDITOR.replace( '.ckeditor');
    </script>";
            }
        }

        return $ckeditorStmt;
    }

    /**
     * Generates image upload statement
     *
     * @return string
     */
    private function getImageUploadStatement()
    {
        $imageUploadStmt = "";

        if ($this->hasMedia) {
            $imageUploadStmt .= "\n\t\t\t\t\t\t<label>" . Str::replace('_', ' ', Str::studly($this->mediaField)) . "</label>
                        <label class=\"custom-file-button\">
                            <div class=\"custom-file-content\">
                                <span class=\"text-white\">
                                    <i class=\"fa fa-upload mr-2\"></i>
                                    <span>Select " . Str::replace('_', ' ', Str::studly($this->mediaField)) . "</span>
                                </span>
                                <input name=\"" . Str::snake($this->mediaField) . "\" id=\"image\" type=\"file\" accept=\"image/*\" />
                            </div>
                        </label>";
        }

        return $imageUploadStmt;
    }


    /**
     * Generates image update statement
     *
     * @return string
     */
    private function getImageUpdateStatement()
    {
        $imageUploadStmt = "";

        if ($this->hasMedia) {
            $imageUploadStmt .= "\n\t\t\t\t\t\t<label>" . Str::replace('_', ' ', Str::title($this->mediaField)) . "</label>
                        <label class=\"custom-file-button\" style=\"background-image: url({{asset(\$" . Str::snake($this->modelName) . "->" . Str::snake($this->mediaField) . ")}})\">
                            <div class=\"custom-file-content\">
                                <span class=\"text-white\">
                                    <i class=\"fa fa-upload mr-2\"></i>
                                    <span>Select " . Str::replace('_', ' ', Str::title($this->mediaField)) . "</span>
                                </span>
                                <input name=\"" . Str::snake($this->mediaField) . "\" id=\"image\" type=\"file\" accept=\"image/*\" />
                            </div>
                        </label>";
        }

        return $imageUploadStmt;
    }

    /**
     * Generate table head statement and
     * return statement lines
     */
    protected function getListTableHeadStatement()
    {
        $migrations = $this->request->get('migrations');

        $tableHeadStmt = "<th>#</th>";
        foreach ($migrations as $migration) {
            if ($migration['show_index'] == true) {
                $tableHeadStmt .= "\n\t\t\t\t\t\t\t\t<th>" . Str::ucfirst($migration['field_name']) . "</th>";
            }
        }

        return $tableHeadStmt;
    }

    /**
     * Generate table body statement and
     * return statement lines
     */
    protected function getEntitiesListStatement()
    {
        $migrations = $this->request->get('migrations');

        $tableBodyStmt = "<td>{{++\$index}}</td>";
        foreach ($migrations as $migration) {
            if ($migration['show_index'] == true) {
                $tableBodyStmt .= "\n\t\t\t\t\t\t\t\t\t<td>{{ $" . $this->modelEntity . "->" . Str::snake($migration['field_name']) . " }}</td>";
            }
        }

        return $tableBodyStmt;
    }

    /**
     * Generate table body statement and
     * return statement lines
     */
    protected function getEmptyDataStatement()
    {
        $migrations = collect($this->request->get('migrations'))->filter(function ($migr) {
            return $migr['show_index'];
        })->toArray();
        return "<tr><td colspan=\"" . (count($migrations) + 4) . "\" class=\"text-center\">No data available for " . Str::ucfirst($this->modelEntities) . "</td></tr>";
    }

    /**
     * Generate Trashed Buttons statement and
     * return statement lines
     */
    protected function getTrashedButtonsStatement()
    {
        $trashedButtonsStmt = "<a href=\"{{ route('adminr." . $this->modelEntities . ".index') }}\" class=\"btn btn-sm btn-primary m-0 mr-3\">
                             <svg class=\"h-3 w-3\">
                                 <use xlink:href=\"{{ coreUiIcon('cil-apps') }}\"></use>
                             </svg>
                             View all
                        </a>
                        <a href=\"{{ route('adminr." . $this->modelEntities . ".index') }}?trashed=true\" class=\"btn btn-sm btn-primary m-0 mr-3\">
                             <svg class=\"h-3 w-3\">
                                 <use xlink:href=\"{{ coreUiIcon('cil-trash') }}\"></use>
                             </svg>
                             Trashed
                        </a>";

        return $this->hasSoftdeletes ? $trashedButtonsStmt : null;
    }

    public function rollback()
    {
        if (!is_null($this->modelEntities)) {
            $this->deleteDir(base_path() . '/resources/views/admin/' . $this->modelEntities);
        }
        return $this;
    }

}
