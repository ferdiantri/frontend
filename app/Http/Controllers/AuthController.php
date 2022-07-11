<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Hash;
use DB;
use Validator;
use Auth;
use Intervention\Image\ImageManager;
use Image;
class AuthController extends Controller
{
    public function userProfile()
    {
        $user = Auth::user();
        $success = $user->makeHidden(['email_verified_at','password','remember_token']);

        $response['status'] = true;
        $response['profile'] = $user;

        return response()->json($response, 200);
    }
    public function adminProfile()
    {
        $admin = Auth::user();
        $success = $admin->makeHidden(['email_verified_at','password','remember_token']);

        $response['status'] = true;
        $response['profile'] = $admin;

        return response()->json($response, 200);
    }
    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        if(auth()->guard('user')->attempt(['email' => request('email'), 'password' => request('password')])){
            config(['auth.guards.api.provider' => 'user']);
            $user = User::select('user.*')->find(auth()->guard('user')->user()->id);
            $response['success'] = true;
            $response['email'] = $user['email'];
            $response['token'] =  $user->createToken('MyApp',['user'])->accessToken; 
            return response()->json($response, 200);
        }else{ 
            return response()->json(['error' => ['Email atau Password Salah']], 200);
        }
    }
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        if(auth()->guard('admin')->attempt(['email' => request('email'), 'password' => request('password')])){
            config(['auth.guards.api.provider' => 'admin']);
            $admin = Admin::select('admin.*')->find(auth()->guard('admin')->user()->id);
            $response['success'] = true;
            $response['token'] =  $admin->createToken('MyApp',['admin'])->accessToken; 
            return response()->json($response, 200);
        }else{ 
            return response()->json(['error' => ['Email and Password are Wrong.']], 200);
        }
    }
    public function userSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:5',
            'email' => 'required|email|unique:user',
            'nomor_telepon' => 'required|numeric|unique:user|min:10',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $user = User::create([
            'profile_picture' => ('data_gambar/profile_picture.png'),
            'nama' => $request['nama'],
            'email' => $request['email'],
            'nomor_telepon' => $request['nomor_telepon'],
            'password' => Hash::make($request['password']),
        ]);

        $response['success'] = true;
        $response['data'] = $user;

        return response()->json($response, 200);
    }
    public function adminSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:5',
            'email' => 'required|email|unique:admin',
            'nomor_telepon' => 'required|numeric|unique:admin|min:10',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $user = Admin::create([
            'profile_picture' => ('data_gambar/profile_picture.png'),
            'nama' => $request['nama'],
            'email' => $request['email'],
            'nomor_telepon' => $request['nomor_telepon'],
            'password' => Hash::make($request['password']),
        ]);
        if($user){
            $response['success'] = true;
            $response['data'] = $user;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' =>'api.something_went_wrong'], 500);
        }
    }
    public function logout(){   
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['success' =>'logout_success'],200); 
        }else{
            return response()->json(['error' =>'api.something_went_wrong'], 500);
        }
    }
    public function changeProfilePicture_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'gambar' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $manager = new ImageManager(['driver' => 'imagick']);
        $gambar = $request['gambar'];
        $image = $manager->make($gambar);
        $image_path = public_path('/data_gambar/');
        if (!file_exists($image_path)) {
            mkdir($image_path, 666, true);
        }
        $image_name = time().'.'.$gambar->getClientOriginalExtension();
        $image->resize(300, 300, function($constraint) {
            $constraint->aspectRatio();
        }); 
        $image->rotate(90);
        $image->save($image_path.$image_name);
        $changePicture = User::where('email', $request['email'])->update(['profile_picture' => ('data_gambar/'.$image_name)]);
        if($changePicture <= 1){
            return response()->json(['success' => 'Berhasil'], 200);
        }
        else{
            return response()->json(['error' => 'Gagal'], 200);
        }
    }
}