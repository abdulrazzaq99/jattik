<?php

namespace App\Http\Controllers\Manager\Auth;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
      /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */

    use ResetsPasswords;

    public function __construct()
    {

        parent::__construct();
        $this->middleware('guest');
    }
      /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token)
    {
        $pageTitle  = "Account Recovery";
        $resetToken = PasswordReset::where('token', $token)->first();
        if (!$resetToken) {
            $notify[] = ['error', 'Verification code mismatch'];
            return to_route('manager.password.reset')->withNotify($notify);
        }
        $email = $resetToken->email;
        return view('manager.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|confirmed|min:4',
        ]);

        $reset   = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        $manager = User::where('email', $reset->email)->first();
        if (!$reset) {
            $notify[] = ['error', 'Invalid code'];
            return to_route('manager.login')->withNotify($notify);
        }

        $manager->password = Hash::make($request->password);
        $manager->save();

        $ipInfo  = getIpInfo();
        $browser = osBrowser();
        notify($manager, 'PASS_RESET_DONE', [
            'operating_system' => $browser['os_platform'],
            'browser'          => $browser['browser'],
            'ip'               => $ipInfo['ip'],
            'time'             => $ipInfo['time'],
        ], ['email'], false);

        $notify[] = ['success', 'Password changed'];
        return to_route('manager.login')->withNotify($notify);
    }

      /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        $password_validation = Password::min(6);
        $general             = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', $password_validation],
        ];
    }
}