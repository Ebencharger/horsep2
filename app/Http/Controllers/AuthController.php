<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Loginsession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(
                middleware: 'auth:api',
                except: ['login']
            ),
        ];
    }


    public function logout()
    {
        $user = Auth::user();
        $token = Auth::fromUser($user);
        LoginSession::where('userId', $user->userId)->where('session', $token)->delete();
        Auth::logout();
        User::where('userId', $user->userId)->update([
            'status' => 'Offline',
            'lastActive' => now()
        ]);
        $user->profilePicture = $user->profilePicture != null ? asset('storage/' . $user->profilePicture) : null;
        return response([
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function logoutSuspicious(Request $request)
    {
        try {
            $userId = $request->query('userId') ?? $request->userId;
            $user = User::where('userId', $userId)->get()->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User does not exist',
                ], 404);
            }
            $session = $request->query('token') ?? $request->token;
            Log::where('userId', $user->userId)
                ->where('session', $session)
                ->delete();
            JWTAuth::setToken($session)->invalidate();
            $user->profilePicture = $user->profilePicture != null ? asset('storage/' . $user->profilePicture) : null;
            User::where('userId', $user->userId)->update([
                'status' => 'Offline',
                'lastActive' => now()
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Logged out from other IP addresses',
            ]);
        } catch (\Throwable $th) {
            return response(['status' => false, 'message' => $th->errorInfo[2]], 500);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
        ], headers: [
            'X-Access-Token' => Auth::refresh(),
            'X-Access-Token-Type' => 'Bearer'
        ]);
    }
}
