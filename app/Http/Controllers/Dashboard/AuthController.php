<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\SendOTP;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // /////////////////////////////////////////  Login  ///////////////////////////////////////////
    public function login_view()
    {
        return view('dashboard.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],

        ]);
        // dd($request->all());
        $remember = $request->remember ? true : false;
        if ($validator->fails()) {

            return Redirect::back()->withErrors($validator)->withInput($request->all());
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')], $remember)) {
            $user = auth()->user();
            if ($request->last_project && $user->current_project_id != null) {
                return redirect('/project?user=' . $user->code);
            }

            return redirect('/accounts');
        } else {

            return back()->withErrors(['msg' => 'There is something wrong']);

        }

    }

    public function register_view(Request $request)
    {
        if ($request->invitation) {
            $invitation         = Invitation::where('code', $request->invitation)->first();
            $invitation->status = 'accepted';
            $invitation->save();
            $user = User::where('id', $invitation->user_id)->first();
            if ($user->password != null) {
                return redirect(route('switch.account', $invitation->account_id));
            }
            $email = $invitation->email;
        } else {
            $invitation = null;

            $email = null;
        }

        return view('dashboard.register', compact('invitation', 'email'));
    }

    public function sign_up(Request $request)
    {
        if ($request->filled('country_code') && $request->filled('phone')) {
            $request->merge([
                'phone' => $request->country_code . $request->phone,
            ]);
        }
        $validator = Validator::make($request->all(), [

            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',

            ],
            'password' => 'required|string|min:5|confirmed',
            'phone'    => [
                'nullable',
                Rule::unique('users', 'phone')->whereNull('deleted_at'),
            ],

        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return Redirect::back()->withErrors($validator)->withInput($request->all());
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->name         = $request->name;
            $user->password     = Hash::make($request->password);
            $user->phone        = $request->phone;
            $user->country_code = $request->country_code;
            $user->save();
        } else {
            do {
                $code2 = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            } while (User::where('code', $code2)->exists());
            $user = User::create([

                'name'         => $request->name,
                'code'         => $code2,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'phone'        => $request->phone,
                'country_code' => $request->country_code,

            ]);
            $user_role = Role::where('name', 'User')->first();
            $user->assignRole([$user_role->id]);
        }
        Auth::login($user);

        return redirect('/accounts');

    }

    // /////////////////////////////////////////  Logout  ///////////////////////////////////////////

    public function logout()
    {
        session()->flush();
        Auth::logout();

        // auth()->guard('admin')->logout();
        return redirect('/login');
    }

    public function change_sideBarTheme(Request $request)
    {
        $user               = auth()->user();
        $user->sideBarTheme = $request->sideBarTheme;
        $user->save();

        return response()->json(['success' => true]);

    }

    public function updateProfile(Request $request)
    {
        if ($request->filled('country_code') && $request->filled('phone')) {
            $request->merge([
                'phone' => $request->country_code . $request->phone,
            ]);
        }
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|unique:users,phone,' . $user->id,
            'country_code'     => 'nullable|string',
            'fileProfileImage' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Update basic info
        $user->name         = $request->name;
        $user->country_code = $request->country_code;
        $user->phone        = $request->country_code . $request->phone; // combine again
        $user->save();

        // Handle profile image

        if ($request->hasFile('fileProfileImage')) {
            $image = getFirstMediaUrl($user, $user->avatarCollection);
            if ($image != null) {
                deleteMedia($user, $user->avatarCollection);
            }
            uploadMedia($request->fileProfileImage, $user->avatarCollection, $user);

        } elseif ($request->removed == 'on') {
            $image = getFirstMediaUrl($user, $user->avatarCollection);
            if ($image != null) {
                deleteMedia($user, $user->avatarCollection);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // requires password_confirmation
        ]);

        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The old password is incorrect.',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    /////////////////////////////////////////// forget password ////////////////////////////////////////
    public function email_view()
    {
        return view('dashboard.change_password.email');
    }

    public function sendOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['msg' => 'This email is not registered.'])->withInput();
        }

        // Generate OTP (6-digit)
        $otp = rand(100000, 999999);

        // Save OTP in DB (you can store in users table or a separate table)
        $user->otp            = $otp;
        $user->otp_expires_at = now()->addMinutes(10); // valid for 10 minutes
        $user->save();
        session(['AuthUserCode' => $user->code]);
        Mail::to($user->email)->send(new SendOTP($otp, $user->name));
        return redirect('/ccmw/OTP')->with('success', 'OTP has been sent to your email.');

        // return back()->with('success', 'OTP has been sent to your email.');
    }

    public function otp_view()
    {
        return view('dashboard.change_password.otp_view');
    }
}
