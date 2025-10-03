<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\password;

class AuthController extends Controller
{

    public function index(Request $request)
    {
        return view('login');
    }

    public function forgot_password(Request $request)
    {
        return view('forgot_password');
    }

    public function register(Request $request)
    {
        return view('register');
    }

    public function register_post(Request $request)
    {
        // dd($request->all());
        $user = request()->validate([
            'username'          => 'required',
            'email'             => 'required|unique:users',
            'department'        => 'required',
            'level'             => 'required',
            'warehouse'         => 'nullable',
            'password'          => 'required|min:6|',
            'confirm_password'  => 'required_with:password|same:password|min:6|'
        ]);
       
        $user                   = new User;
        $user->username         = trim($request->username);
        $user->fullname         = trim($request->fullname);
        $user->nik              = trim($request->nik);
        $user->email            = trim($request->email);
        $user->department       = trim($request->department);
        $user->level            = trim($request->level);
        $user->warehouse_access = trim($request->warehouse ?? '');
        $user->password         = Hash::make($request->password);
        $signatureData = $request->input('signature');

        // convert base64 to image
        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image);

        // remove old signature if exists
        if ($user->sign && file_exists(public_path('assets/images/sign/' . $user->sign))) {
            unlink(public_path('assets/images/sign/' . $user->sign));
        }

        // generate new filename
        $username   = Str::slug($user->username, '_'); 
        $timestamp  = now()->setTimezone('Asia/Jakarta')->format('Ymd'); 
        $filename   = $timestamp . '_' . $username . '.png'; 

        // ensure directory exists
        $directory = public_path('assets/images/sign/');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // save new file
        file_put_contents($directory . $filename, base64_decode($image));

        $user->sign             = $filename;
        $user->remember_token   = Str::random(50);
        $user->save();

        return redirect('/')->with('success', 'Register Succesfully');
    }

    public function check_email(Request $request)
    {
        $email = $request->input('email');
        $isExists = User::where('email', $email)->first();
        if ($isExists) {
            return response()->json(array("exists" => true));
        } else {
            return response()->json(array("exists" => false));
        }
    }

    public function login_post(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // dd(Auth::user()->toArray());
            // dd(Auth::user()->role);
            return redirect()->intended('admin/dashboard');
        } else {
            return redirect()->back()->with('error', 'Please enter correct credentials');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }
}
