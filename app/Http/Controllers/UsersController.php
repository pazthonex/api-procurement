<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function index(){
        $users = User::all();
        if($users){
            return response()->json([
                'status' => 200,
                'data' =>$users,
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => 'Server Error'
        ]);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'campus' => 'required',
            'employee_id' => 'required|unique:users'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->messages()
            ]);
        }
        $user = User::create([
            'name'=> $request->name,
            'employee_id'=> $request->employee_id,
            'email'=>   $request->email,
            'campus'=> $request->campus,
            'role'=> $request->role,
            'password'=> Hash::make($request->password)
      ]);
      $token = $user->createToken('employee_token', ['employee'])->plainTextToken;
      if($user){
        return response()->json(['status' => 200, 'message' => 'Users Save successfully.','token' => $token]);
      }

    }

    public function login(Request $request){
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email',$email)->first();     
          if (! $user || ! Hash::check($password,$user->password)) {
              return response()->json([
                  'status' => 400,
                  'message' =>'The provided credentials are Incorrect.'
              ]);
          }
              $token = $user->createToken('superadmin', ['superadmin'])->plainTextToken;
              $data = [
                  'token' => $token,
                  'email' => $user->email,
                  'role' => $user->role,
              ];
              return response()->json([
                  'status' => 200,
                  'message' => 'Logged In Successfully',
                  'data' => $data
              ]);

    }
//api
    public function logout(){
        #delete token from database
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logged Out Successfully',
        ]);
    } 


    public function createtoken(Request $request){
        try {
            $aes = new AESCipher();
            $role = $request->data['role'];
            $campus = $request->data['campus'];
            $name = $request->data['name'];
            $employee_id = $request->data['employee_id'];

            $user = User::create([
                'name'=> $name,
                'employee_id'=>  $aes->decrypt($employee_id),
                'campus'=>  $aes->decrypt($campus),
                'role'=> $aes->decrypt($role),
                'password'=> Hash::make($request->password)
            ]);
             $token = $user->createToken('employee_token', ['employee'])->plainTextToken;
            return response()->json( ['status' => 200,'message' => [  'name' => $name, 'token' => $token] ]);

        } catch (\Throwable $th) {
            return response()->json( ['status' => 400,'error' => $th ]);

        }
    }
}
