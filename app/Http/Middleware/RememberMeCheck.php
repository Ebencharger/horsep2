<?php

namespace App\Http\Middleware;

use App\Events\GetUser;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RememberMeCheck
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->rememberTokenDate && now()->greaterThan($user->rememberTokenDate)) {
            return response()->json(['message' => 'Session expired. Please log in again.'], 401);
        }
        if ($user->status == "Away") {
            if ($user->profilePicture==!null) {
                $user->profilePicture=asset('storage/' . $user->profilePicture);
            }
        }
        User::where('userId', $user->userId)->update([
            'status' => 'Online',
            'lastActive'=>now()
        ]);
        return $next($request);
    }
}
