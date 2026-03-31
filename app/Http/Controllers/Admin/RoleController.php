<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
 
class RoleController extends Controller {
    public function index() {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }
    public function create() { return view('admin.roles.create'); }
    public function store(Request $request) {
        $request->validate(['name'=>'required|string','permissions'=>'array']);
        Role::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'permissions' => $request->permissions ?? [],
        ]);
        return redirect()->route('admin.roles.index')->with('success','Role created!');
    }
    public function destroy(Role $role) {
        if ($role->users()->exists()) return back()->withErrors(['error'=>'Cannot delete role with active users.']);
        $role->delete();
        return back()->with('success','Role deleted!');
    }
}