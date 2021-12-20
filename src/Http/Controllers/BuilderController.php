<?php

namespace Devsbuddy\AdminrCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Devsbuddy\AdminrCore\Models\Menu;
use Devsbuddy\AdminrCore\Models\Resource;
use Devsbuddy\AdminrCore\Database;
use Devsbuddy\AdminrCore\Services\BuildControllersService;
use Devsbuddy\AdminrCore\Services\BuildMigrationService;
use Devsbuddy\AdminrCore\Services\BuildModelService;
use Devsbuddy\AdminrCore\Services\BuildRoutesService;
use Devsbuddy\AdminrCore\Services\BuildViewsService;
use Devsbuddy\AdminrCore\Services\ResourceService;
use Devsbuddy\AdminrCore\Traits\HasStubs;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BuilderController extends Controller
{
    use HasStubs;

    public $resourceService;
    public $buildControllersService;
    public $buildModelService;
    public $buildMigrationService;
    public $buildRouteService;
    public $buildViewsService;

    public function __construct()
    {
        $this->resourceService = new ResourceService();
        $this->buildControllersService = new BuildControllersService();
        $this->buildModelService = new BuildModelService();
        $this->buildMigrationService = new BuildMigrationService();
        $this->buildRouteService = new BuildRoutesService();
        $this->buildViewsService = new BuildViewsService();
    }

    public function index()
    {
        try {
            $dataTypes = collect(Database::dataTypes())->sort()->toArray();
            return view('adminr-core::builder.create', compact('dataTypes'));
        } catch (\Exception $e){
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function build(Request $request)
    {
        if($this->resourceExists($request)){
            return response()->json(['status' => 'error', 'message' => 'Resource already exist!'], 200);
        }

        try{
            $this->resourceService->store($request);

            $this->buildControllersService
                ->prepare($request)
                ->buildApiController()
                ->buildController()
                ->cleanUp();

            $this->resourceService->update([
                'name' => Str::title($this->buildControllersService->modelEntities),
                'controllers' => [
                    'api' => $this->buildControllersService->controllerName,
                    'admin' => $this->buildControllersService->controllerName,
                ],
                'table' => $this->buildControllersService->tableName,
                'payload' => [
                    'model' => $this->buildControllersService->modelName . '.php',
                    'has_api' => $request->get('build_api'),
                    'views' => [
                        'index' => 'adminr/' . $this->buildControllersService->modelEntities . '/index.blade.php',
                        'create' => 'adminr/' . $this->buildControllersService->modelEntities . '/create.blade.php',
                        'edit' => 'adminr/' . $this->buildControllersService->modelEntities . '/edit.blade.php',
                    ],
                    'migration' => null,
                    'controllers' => [
                        'api' => $this->buildControllersService->controllerName . '.php',
                        'admin' => $this->buildControllersService->controllerName . '.php',
                    ],
                    'routes' => [
                        'api' => $this->buildControllersService->modelEntities . '/' . $this->buildControllersService->modelEntities . '.json',
                        'admin' => $this->buildControllersService->modelEntities . '/' . $this->buildControllersService->modelEntities . '.json',
                    ],
                ],
            ]);

            $this->buildModelService
                ->prepare($request)
                ->buildModel()
                ->cleanUp();

            $this->buildMigrationService
                ->prepare($request)
                ->buildMigration()
                ->cleanUp();

            $this->resourceService->update([
                'migration' => $this->buildMigrationService->migrationFileName,
                'payload->migration' => $this->buildMigrationService->migrationFileName . '.php'
            ]);

            $this->buildRouteService
                ->prepare($request)
                ->buildApiRoute()
                ->buildAdminRoute()
                ->cleanUp();

            $this->buildViewsService
                ->prepare($request)
                ->buildIndexView()
                ->buildCreateView()
                ->buildEditView()
                ->cleanUp();

            Menu::firstOrCreate([
                'name' => 'resource',
                'label' => Str::title(Str::replace('_', ' ', $this->buildControllersService->modelEntities)),
                'route' => $this->buildControllersService->modelEntities . '.index',
                'resource' => $this->resourceService->id,
            ]);

            Artisan::call('migrate');

            return response()->json(['status' => 'success', 'message' => 'Resource generated Successfully!'], 200);
        } catch (\Exception $e){
            $this->rollbackAll();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 200);
        } catch (\Error $e){
            $this->rollbackAll();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 200);
        }
    }

    /**
     * Checks if Resource already exists
     *
     * @param Request $request
     * @return bool
     */
    private function resourceExists(Request $request)
    {
        $resource = Resource::where('name', Str::snake($request->get('model')))->where('model', $request->get('model'))->first();
        $model = File::exists(app_path() . "/Models/".Str::title($request->get('model')).".php");
        if(!is_null($resource)){
           return true;
        }
        if($model){
            return true;
        }
        return false;
    }


    /**
     * Rollbacks everything
     * if anything goes wrong
     */
    private function rollbackAll(){
        $this->buildControllersService->rollback()->cleanUp();
        $this->buildModelService->rollback()->cleanUp();
        $this->buildMigrationService->rollback()->cleanUp();
        $this->buildRouteService->rollback()->cleanUp();
        $this->buildViewsService->rollback()->cleanUp();
        $this->resourceService->rollback();
    }
}




