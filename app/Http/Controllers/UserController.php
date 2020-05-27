<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Profile;
use Illuminate\Http\Request;
use App\http\Resources\User as UserResource;
use App\Http\Resources\UserCollection;

class UserController extends Controller
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
        $per_page = $request->per_page;
     
        return response()->json([
            'users' => new UserCollection(User::paginate($per_page)),
            'roles'=>Role::pluck('name')->all()], 200);
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
        $role = Role::where('name', $request->role)->first();
        $user = new User([
            'name' => $request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            ]);
        $user->role()->associate($role);
        $user->save();
        $user->profile()->save(new Profile());
        return response()->json(['user'=> new UserResource($user)], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::where('name', 'LIKE', '%'.$id.'%')->paginate();
        return response()->json(['users'=>$users], 200);
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
        $role = Role::where('name', $request->role)->first();
        $user = User::find($id);
        $user->name = $request->name;
        $user->role()->dissociate($user->role);
        $user->role()->associate($role);
        $user->save();
        return response()->json(['user'=>new UserResource($user)], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id)->delete();
        Profile::where('user_id', $id)->delete();
        return response()->json(['user'=> $user], 200);
    }
    public function deleteAll(Request $request)
    {
        User::whereIn('id', $request->users)->delete();
        return response()->json(['message'=>'Records Deleted Successfully'], 200);
    }

    public function changeRole(Request $request)
    {
        $user = User::find($request->user);
        $logedInUser = $request->user();
        if ($user->id === $logedInUser->id) {
            return response()->json(['user'=>new UserResource($logedInUser)], 422);
        }
        $role = Role::where('name', $request->role)->first();
        $user->role()->associate($role);
        $user->save();
        return response()->json(['user'=>new UserResource($user)], 200);
    }

    public function varifyEmail(Request $request)
    {
        $request->validate([
        'email'=>'required|unique:users'
    ]);
        return response()->json([
        'message', 'Valid Email'
    ], 200);
    }

    public function changePhoto(Request $request)
    {
        $user = User::find('user', $request->user);
        $profile = Profile::where('user_id', $request->user)->first();
        $ext = $request->photo->extention();
        $photo = $request->photo->storeAs('images', Str::random(20).".{$ext}", 'public');
        $profile->photo = $photo;
        $user->profile()->save($profile);
        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }
}
