<?php

namespace Devsbuddy\AdminrCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Devsbuddy\AdminrCore\Models\Resource;
use Devsbuddy\AdminrCore\Traits\HasResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolePermissionController extends Controller
{
    use HasResponse;

    public function getRoles()
    {
        return $this->success(\Spatie\Permission\Models\Role::select('id', 'name')
            ->where('name', '!=', 'super_admin')
            ->get(), 200);
    }

    public function getPermissions(Resource $resource)
    {
        $permissions = $this->success(\Spatie\Permission\Models\Permission::select('id', 'name')
            ->where('resource', strtolower($resource->name))
            ->orderBy('name', 'ASC')
            ->with(['roles:id'])
            ->get(), 200);

        return $permissions;
    }

    public function assignPermissionsToRoles(Request $request)
    {
        foreach(json_decode($request->get('permissions')) as $r => $p){
            $role = Role::where('name', json_decode($r))->first();
            $rolePermissionsToRemove = $role->getAllPermissions()
                ->where('resource', strtolower($request->get('resource')))->whereNotIn('id', $p)->pluck('id')->toArray();
            $permissions = Permission::where('resource', strtolower($request->get('resource')))->pluck('id')->toArray();

            foreach ($permissions as $permission){
                if(in_array($permission, $p) && !in_array($permission, $rolePermissionsToRemove)){
                    $role->givePermissionTo($permission);
                }
            }
            foreach ($rolePermissionsToRemove as $rolePermissionToRemove){
                $role->revokePermissionTo($rolePermissionToRemove);
            }
        }

        return $this->successMessage('Role and permissions assigned!');
    }

}

