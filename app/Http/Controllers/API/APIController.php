<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller {

    protected $successStatus = 200;

    public function __construct() {
        
    }
    /**
     * Login api
     * 
     * @return \Illuminate\Http\Response
     */
    public function login() {
        if (Auth::attempt(["email" => request("email"), "password" => request("password")]) || Auth::attempt(["name" => request("email"), "password" => request("password")])) {
            $user = Auth::user();
            $success["token"] = $user->createToken("MyApp")->accessToken;
            return response()->json(["status"=>1,"message"=>"Logged in successfully","data" => $user->id], $this->successStatus);
        } else {
            return response()->json(["status" => 0,"message"=>"Unauthorized","data"=>null],401);
        }
    }
    
    /**
     * Register api
     * @param Request $request
     * @return JSON
     */
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            "first_name"=>"required",
            "email"=>"required|email",
            "dob"=>"required",
            "gender"=>"required",
            "languages"=>"required",
            "password"=>"required",
            //"confirm_password"=>"required|same:password",
        ]);
        
        if($validator->fails()){
            return response()->json(["status"=>0,"message"=>$validator->errors(),"data"=>null],401);
        }
        
        $input = $request->all();
        $input["password"] = bcrypt($input["password"]);
        
        $user = new User();
        $user->name = uniqid();
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->gender = $request->gender;
        $user->languages = $request->languages;
        $user->password = bcrypt($request->password);
        $user->save();
        
        $success["token"] = $user->createToken("MyApp")->accessToken;
        $success["name"] = $user->name;
        return response()->json(["status"=>1,"message"=>"Registered successfully","data"=>$success],$this->successStatus);
        
    }

}
