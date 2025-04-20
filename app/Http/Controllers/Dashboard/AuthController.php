<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    ///////////////////////////////////////////  Login  ///////////////////////////////////////////
    public function login_view()
    {
        return view('dashboard.login');
    }

    public function login(Request $request)
    {
        $validator  =   Validator::make($request->all(), [

                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', 'min:8'],

        ]);
        // dd($request->all());
        $remember=$request->remember? true: false;
        if ($validator->fails()) {

            return Redirect::back()->withErrors($validator)->withInput($request->all());
        }
       
        if (Auth::attempt(['email' => request('email'),'password' => request('password')],$remember)) {
            $user=auth()->user();
            if($request->last_project && $user->current_project_id != null){
                return redirect('/project');
            }
            return redirect('/accounts');
        } else {

            return back()->withErrors(['msg' => 'There is something wrong']);
            
        }

    }

    public function register_view(Request $request){
        if($request->invitation){
            $invitation=Invitation::where('code',$request->invitation)->first();
            $invitation->status='accepted';
            $invitation->save();
            $user=User::where('id',$invitation->user_id)->first();
            if($user->password!=null){
                return redirect(route('switch.account',$invitation->account_id));
            }
            $email=$invitation->email;
        }else{
            $invitation=null;

            $email=null;
        }
        

        return view('dashboard.register',compact('invitation','email'));
    }

    public function sign_up(Request $request){
        if ($request->filled('country_code') && $request->filled('phone')) {
            $request->merge([
                'phone' => $request->country_code . $request->phone,
            ]);
        }
        $validator  =   Validator::make($request->all(), [

            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255'
                
            ],
            'password' => 'required|string|min:5|confirmed',
            'phone' => [
                'nullable',
                Rule::unique('users', 'phone')->whereNull('deleted_at'),
            ]

        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return Redirect::back()->withErrors($validator)->withInput($request->all());
        }
        $user=User::where('email',$request->email)->first();
        if($user){
            $user->name=$request->name;
            $user->password=Hash::make($request->password);
            $user->phone=$request->phone;
            $user->save();
        }else{
            do {
                $code2 = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            } while (User::where('code', $code2)->exists());
            $user = User::create([
            
                'name' => $request->name,
                'code' => $code2,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                
            ]);
            $user_role = Role::where('name','User')->first();
            $user->assignRole([$user_role->id]);
        }
        Auth::login($user);
        return redirect('/accounts');


    }

    ///////////////////////////////////////////  Logout  ///////////////////////////////////////////

    public function logout()
    {
        session()->flush();
        Auth::logout();

        // auth()->guard('admin')->logout();
        return redirect('/login');
    }

   

}
