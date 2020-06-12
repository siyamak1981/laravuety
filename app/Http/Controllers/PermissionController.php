<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page;
        $permissions = Permission::all();

        return response()->json(['permissions' => Permission::paginate($per_page)], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
            'name' => 'required|unique:permissions,name',
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

        $permission = new Permission;
        $permission->name = $request->name;
        $permission->guard_name = 'api';
        $permission->save();

        $permission = Permission::select('id', 'name')
                      ->where('id', $permission->id)
                      ->first();

        return response()->json($permission, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,'.$request->id,
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

        $permission = Permission::select('id', 'name')->where('id', $request->id)->first();
        $permission->name = $request->name;
        $permission->guard_name = 'api';
        $permission->update();

        return response()->json($permission, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_multiple(Request $request)
    {
        $permission = Permission::where('name', $request->name)->select('id', 'name')->get();
        $permission->each->delete();
        return response()->json([
            'permission'=> $permission], 200);
        // $ids = $request;
        // $perm = Permission::whereIn('id', $ids)->select('id', 'name')->get();
        // $response = $perm;
        // Permission::whereIn('id', $ids)->delete();
        // return response()->json($response, 200);
    }
}
