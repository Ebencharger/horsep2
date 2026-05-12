<?php

namespace App\Http\Controllers;

use App\Enums\USERTYPE;
use App\Events\GetUser;
use App\Mail\message;
use App\Models\Loginattempt;
use App\Models\Loginsession;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;



abstract class Controller
{
    public $url = 'http://127.0.0.1:8000';
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function useEmail($message, $data, $email)
    {
        $message = $message;
        $data = $data;
        $r = new message($data);
        $result = Mail::to($email)->send($r);
        return $result;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'rememberMe' => 'nullable|boolean'
        ]);
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->rememberMe;
        $token = Auth::attempt($credentials, true);
        if (!$token) {
            $getUser = User::where('email', $request->email)->get()->first();
            if ($getUser) {
                $countWrongAttempt = Loginattempt::where('userId', $getUser->userId)->orderByDesc('created_at')
                    ->limit(4)->get();
                if (count($countWrongAttempt) >= 3) {
                    $message = "
                                <p>Hello $getUser->firstName,</p>
                                <p>We notified multiple unsuccessful login attempts on  your account.</p>
                                <p>We have locked the account for about 15 minutes for now.</p>
                                ";
                    $data = ['name' => $getUser->firstName . ' ' . $getUser->lastName, 'subject' => 'Multiple Login Attempts', 'view' => 'message', 'message' => $message];
                    $this->useEmail($message, $data, $getUser->email);
                }
                Loginattempt::create(
                    [
                        'userId' => $getUser->userId,
                        'reason' => 'Incorrect Password',
                        'created_at' => now()
                    ]
                );
            }
            return response()->json([
                'status' => false,
                'message' => 'Email or Password is invalid',
            ], 401);
        }
        $user = Auth::user();
        Loginattempt::where('userId', $user->userId)->delete();
        // if ($user->emailVerified!=1) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Your email is not verified yet',
        //     ], 419);
        // }
        if ($rememberMe == 1) {
            User::where('userId', $user->userId)->update([
                'rememberTokenDate' => now()->addDays(30)
            ]);
        }

        // Get user details
        $this->reuseForAgent($request, $user, $token);
        $this->is2faSetUp($user);
        return response()->json([
            'status' => true,
            'message' => 'You logged in successfully',
            'data' => $token,
            'user' => $user
        ], headers: [
            'X-Access-Token' => $token,
            'X-Access-Token-Type' => 'Bearer'
        ]);
    }

    private function reuseForAgent($request, $user, $token)
    {
        //OPTIONAL::Check if user has verified email, otherwise, ask user to verify email by sending new email verification;
        $ip = $request->ip();
        $location = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $agent = new \Jenssegers\Agent\Agent();
        $device = $agent->device();
        $platform = $agent->platform();
        $browser = $agent->browser();
        $existIP = Loginsession::where('userId', $user->userId)->orderBy('created_at', 'asc')->get()->first();
        if (!$existIP) {
            Loginsession::create([
                'userId' => $user->userId,
                'session' => $token,
                'ipaddress' => $ip,
                'device' => $device,
                'platform' => $platform,
                'browser' => $browser,
                'city' => $location->city ?? null,
                'country' => $location->country ?? null,
            ]);
        } else {
            if ($ip != $existIP->ipaddress) {
                Loginsession::create([
                    'userId' => $user->userId,
                    'session' => $token,
                    'ipaddress' => $ip,
                    'device' => $device,
                    'platform' => $platform,
                    'browser' => $browser,
                    'city' => $location->city ?? null,
                    'country' => $location->country ?? null,
                ]);
            }
        }
        $user->profilePicture = $user->profilePicture != null ? asset('storage/' . $user->profilePicture) : null;
        User::where('userId', $user->userId)->update([
            'status' => 'Online',
            'lastActive' => now()
        ]);
    }

    public function is2faSetUp($user)
    {
        if ($user->set2fa === 1) {
            $otp = random_int(100000, 999999);
            $message = "
            <p>Hello $user->firstName,</p>
            <p>There is a pending authorization on your account.
Use the code below as your one time password</p>
            <p>$otp</p>
            ";
            $data = ['name' => $user->firstName . ' ' . $user->lastName, 'subject' => 'Pending Authorization', 'view' => 'message', 'message' => $message];
            $this->useEmail($message, $data, $user->email);
            Otp::where('userId', $user->userId)->update([
                'userId' => $user->userId,
                'token' => $otp,
                'status' => 'Active',
                'created_at' => now()
            ]);
        }
    }
}
