<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Enums\Role;
use App\Http\Resources\UserPrivateResource;

class UserController extends Controller
{
    // GET users - List
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $usersList = User::orderByDesc('created_at')->get();
        return UserPrivateResource::collection($usersList);
    }

    // PUT users/{id} - Update
    public function update(Request $request, User $user)
    {
        $this->authorize('update', User::class);
        if ($request->method() != 'PUT')
        {
            return response()->json(['error' => "Incorrect method"], 405);
        }
        $input = $request->validate([
            'name' => 'required',
            'email' => 'email',
            'role' => Rule::enum(Role::class),
            'is_email_public' => 'boolean',
        ]);
        
        $user->fill($input)->save();
        return new UserPrivateResource($user);
    }

    // DELETE users/{id}
    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);
        DB::transaction(function() use ($user) {
            foreach ($user->posts as $post) {
                $post->comments()->delete();
            }
            $user->posts()->delete();
            $user->comments()->delete();
            $user->delete();
        });
    }
}
