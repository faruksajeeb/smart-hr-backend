<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        // sleep(3); // simulate delay
        $query = Role::with('permissions')
            ->when($request->search, fn($x)=>$x->where(function($w) use ($request){
                $w->where('name','like',"%{$request->search}%")
                  ->orWhere('label','like',"%{$request->search}%");
            }))
            ->orderBy($request->sort ?? 'id', $request->dir ?? 'desc');

        return RoleResource::collection($query->paginate($request->per_page ?? 10));
    }

    public function rolePermissions()
    {

        $permissions = Permission::get()->groupBy('module');
        //dd($permissions);
        return response()->json(['data'=>$permissions]);

    }   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
      
        $data = $request->validated();
        $data['guard_name'] = 'web'; // default guard
        $data['is_active'] = true; // default active status
        $data['name'] = Str::slug($data['label']); // ensure name is lowercase
        $role = Role::create($data);
        $role->syncPermissions($data['permissions']);
        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $Role)
    { 
        return new RoleResource($Role);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $data['guard_name'] = 'web'; // default guard
        $data['is_active'] = true; // default active status
        $data['name'] = Str::slug($data['label']); // ensure name is lowercase
        $role->update($data);
        $role->syncPermissions($data['permissions']);
        return new RoleResource($role);
    }

    public function destroy(Role $role)
    {
        // Detach all permissions before deleting
        $role->permissions()->detach();
        $role->delete(); // use SoftDeletes if you prefer
        return response()->json(['message'=>'Deleted']);
    }

    public function activeRoles()
    {
        $roles = Role::where('is_active', 1)->get();

        return response()->json([
            'success' => true,
            'data'    => $roles
        ]);
    }

}
