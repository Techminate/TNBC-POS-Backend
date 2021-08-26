<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Storage;

use App\Models\User;
use App\Models\Profile;
use App\Models\Role;

class ProfileController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json(['users'=>$users], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $data = $request->all();
        $user = User::find($id);

        if($user->email==$data['email']){
            $this->validate($request,[
                'name'=>'required',
                'email'=>'required|string|email|max:255',
                'role_id'=>'required',
            ]);
        }
        else{
            $this->validate($request,[
                'name'=>'required',
                'email'=>'required|string|email|max:255|unique:users',
                'role_id'=>'required',
            ]);
        }

        $user->update($data);

        return response()->json(['user'=>$user], 200);
    }

    public function deleteUser($id)
    {
        User::find($id)->delete();
        $response = [
            'message' => 'Record Deleted Successfully',
        ];
        return response($response, 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        $role = Role::where('id',$user->role_id)->get();
        // $rolename = $role->name;
        $rolename = $user->role->name;
        $profile = Profile::where('user_id',$user->id)->get();

        $response = [
            'user' => $user,
            'rolename' => $rolename,
            'profile'=>$profile
        ];

        return response($response, 200);

        // $user = User::where('id',$id)->with('role')->with('profile')->first();
        // return response()->json(['user'=>$user], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'mobile'=>'required',
            'present_address'=>'required|min:3',
            'permanent_address'=>'required|min:3',
            
            'identity_number'=>'required|numeric',
            'user_id'=>'required',
        ]);
         // dd($request);
        $data = $request->all();

        $imagePath = 'images/profile';
        $url  = url('');

        if($request->hasFile('image')){
            $image = $request->file('image');
            
            $imgName = 'img'.time(). '.' .$image->getClientOriginalExtension();
            File::isDirectory($imagePath) or File::makeDirectory($imagePath, 7777, true, true);
			$newPath = $imagePath . '/' . $imgName;

            $fileLocation = $url. '/' .$imagePath . '/' . $imgName;
            $data['image'] = $fileLocation;

            $request->image->move(public_path(env('REL_PUB_FOLD').$imagePath),$imgName);
        }else{
            $data['image'] = $url. '/' .$imagePath . '/' . 'default.jpg';
        }

        Profile ::create($data);

        $response = [
            'profile' => $data,
        ];

        return response($response, 200);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}