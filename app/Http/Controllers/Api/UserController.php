<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions'])
            ->when($request->search, fn($x) => $x->where(function ($w) use ($request) {
                $w->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->orderBy($request->sort ?? 'id', $request->dir ?? 'desc');

        return UserResource::collection($query->paginate($request->per_page ?? 10));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        if ($request->selectedRoles) {
            $user->assignRole($request->selectedRoles);
        }

        if (!empty($request->permissions)) {
            $user->syncPermissions($request->permissions);
        }
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        if ($request->selectedRoles) {
            $user->assignRole($request->selectedRoles);
        }

        if (!empty($request->permissions)) {
            $user->syncPermissions($request->permissions);
        }
        
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        // Detach all roles related to the user
        $user->roles()->detach();

        $user->delete(); // use SoftDeletes if you prefer
        return response()->json(['message' => 'Deleted']);
    }
}
