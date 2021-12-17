<?php

namespace Devsbuddy\AdminrCore\Services;

use Devsbuddy\AdminrCore\Models\Resource;
use Devsbuddy\AdminrCore\Traits\CanManageFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateCrudRecordService
{
    use CanManageFiles;

    public $id;

    public function store(Request $request)
    {
        $crud = Resource::firstOrcreate([
            'name' => Str::title(Str::plural($request->get('model'))),
            'model' => Str::studly(Str::singular($request->get('model'))),
        ]);

        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_show_list',
            'resource' => strtolower($crud->name)
        ]);
        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_show_single',
            'resource' => strtolower($crud->name)
        ]);
        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_create',
            'resource' => strtolower($crud->name)
        ]);
        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_edit',
            'resource' => strtolower($crud->name)
        ]);
        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_store',
            'resource' => strtolower($crud->name)
        ]);
        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_update',
            'resource' => strtolower($crud->name)
        ]);
        Permission::firstOrcreate([
            'name' => strtolower($crud->name) . '_destroy',
            'resource' => strtolower($crud->name)
        ]);

        if($request->get('softdeletes')){
            Permission::firstOrcreate([
                'name' => strtolower($crud->name) . '_restore',
                'resource' => strtolower($crud->name)
            ]);
            Permission::firstOrcreate([
                'name' => strtolower($crud->name) . '_force_destroy',
                'resource' => strtolower($crud->name)
            ]);
        }

        $this->id = $crud->id;

        return $this;
    }

    public function update(array $data)
    {
        Resource::where('id', $this->id)->update($data);
        return $this;
    }

    public function rollback($id = null)
    {
        if ($id == null){
            $id = $this->id;
        }
        $crud = Resource::where('id', $id)->first();

        if($crud){
            $this->deleteFile(base_path() . '/app/Models/' . $crud->payload->model)
                ->deleteDir(base_path() . '/resources/views/admin/' . $crud->name)
                ->deleteFile(base_path() . '/database/migrations/' . $crud->payload->migration)
                ->deleteFile(base_path() . '/database/migrations/' . $crud->payload->migration)
                ->deleteFile(base_path() . '/app/Http/Controllers/Admin/' . $crud->payload->controllers->admin)
                ->deleteDir(base_path() . '/routes/liquid/api/' . Str::lower($crud->name))
                ->deleteDir(base_path() . '/routes/liquid/admin/' . Str::lower($crud->name))
                ->deleteFile(base_path() . '/app/Http/Controllers/Api/' . $crud->payload->controllers->api);

            $adminRoutesStorage = (array) json_decode(File::get(base_path() . '/routes/liquid/admin/routes.json'));
            if(isset($adminRoutesStorage[Str::lower($crud->name)])){
                unset($adminRoutesStorage[Str::lower($crud->name)]);
            }
            File::put(base_path() . '/routes/liquid/admin/routes.json', json_encode((object) $adminRoutesStorage));


            $apiRoutesStorage = (array) json_decode(File::get(base_path() . '/routes/liquid/api/routes.json'));
            if(isset($apiRoutesStorage[Str::lower($crud->name)])){
                unset($apiRoutesStorage[Str::lower($crud->name)]);
            }
            File::put(base_path() . '/routes/liquid/api/routes.json', json_encode((object) $apiRoutesStorage));

            if(\DB::table('migrations')->where('migration', $crud->migration)->first()) {
                \DB::table('migrations')->where('migration', $crud->migration)->delete();
            }
            $roles = Role::where('name', '!=', 'super_admin')->get();
            $permissions = Permission::where('resource', Str::lower($crud->name))->get();

            foreach($roles as $role){
                foreach ($permissions as $permission){
                    if($role->hasPermissionTo($permission)){
                        $role->revokePermissionTo($permission);
                    }
                }
            }

            foreach ($permissions as $permission){
                $permission->delete();
            }

            Schema::dropIfExists($crud->table);

            $crud->delete();
        }

        return $this;
    }

}
