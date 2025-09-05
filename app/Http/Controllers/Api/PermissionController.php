<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // sleep(3); // simulate delay
        $query = Permission::query()
            ->when($request->search, fn($x)=>$x->where(function($w) use ($request){
                $w->where('name','like',"%{$request->search}%")
                  ->orWhere('label','like',"%{$request->search}%");
            }))
            ->orderBy($request->sort ?? 'id', $request->dir ?? 'desc');

        return PermissionResource::collection($query->paginate($request->per_page ?? 10));
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
      public function store(StorePermissionRequest $request)
    {
       
        $data = $request->validated();
        $data['guard_name'] = 'web'; // default guard
        $data['is_active'] = true; // default active status
        $data['name'] = Str::slug($data['label']); // ensure name is lowercase
        return new PermissionResource(Permission::create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    { 
        
        return new PermissionResource($permission);
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
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $data = $request->validated();
        $data['guard_name'] = 'web'; // default guard
        $data['is_active'] = true; // default active status
        $data['name'] = Str::slug($data['label']); // ensure name is lowercase
        $permission->update($data);
        return new PermissionResource($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete(); // use SoftDeletes if you prefer
        return response()->json(['message'=>'Deleted']);
    }
}
