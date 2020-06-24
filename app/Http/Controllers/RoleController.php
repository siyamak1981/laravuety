<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Validator;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::select('id', 'name', 'created_at', 'updated_at')
        ->with('permissions')
        ->get();
        return response()->json($roles, 200);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
          ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            $obj = $validator->failed();
            $result = [];
            foreach ($obj as $input => $rules) {
                $i = 0;
                foreach ($rules as $rule => $ruleInfo) {
                    $key = $rule;
                    $key = $input.'|'.strtolower($key);
                    $result[$key] = $errors[$input][$i];
                    $i++;
                }
            }
            return response()->json($result, 422);
        }

        $role = new Role;
        $role->name = $request->name;
        $role->guard_name = 'api';
        $role->save();

        //Looping thru selected permissions
        foreach ($request->permissions as $permission) {
            $permission = Permission::where('id', '=', $permission)->firstOrFail();
            //Fetch the newly created role and assign permission
            $role = Role::find($role->id);
            $role->givePermissionTo($permission);
        }

        $role = Role::select('id', 'name')
                  ->with('permissions')
                  ->where('id', $role->id)
                  ->first();

        return response()->json($role, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $roles = Role::where('name', 'LIKE', '%'.$id.'%')->paginate();
        return response()->json(['roles'=>$roles], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $validator = Validator::make($req->all(), [
          'name' => 'required|unique:roles,name,'.$req->id,
          'permissions' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            $obj = $validator->failed();
            $result = [];
            foreach ($obj as $input => $rules) {
                $i = 0;
                foreach ($rules as $rule => $ruleInfo) {
                    $key = $rule;
                    $key = $input.'|'.strtolower($key);
                    $result[$key] = $errors[$input][$i];
                    $i++;
                }
            }
            return response()->json($result, 422);
        }


        $role = Role::select('id', 'name')->where('id', $req->id)->first();
        $role->name = $req->name;
        $role->guard_name = 'api';
        $role->update();

        $p_all = Permission::all();//Get all permissions

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p); //Remove all permissions associated with role
        }

        foreach ($req->permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
          $role->givePermissionTo($p);  //Assign permission to role
        }

        $role = Role::select('id', 'name')
                ->with('permissions')
                ->where('id', $role->id)
                ->first();

        return response()->json($role, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role =Role::where('id', $id)->first();

        dd($role);
        $role->delete();
        return response()->json(['message'=>'Records Deleted Successfully'], 200);
    }

    // public function deleteAll(Request $request)
    // {
    //     Role::whereIn('id', $request->roles)->delete();
    //     return response()->json(['message'=>'Records Deleted Successfully'], 200);
    // }
}
