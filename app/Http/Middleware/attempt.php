<?php

namespace App\Http\Middleware;

use App\Models\Loginattempt;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class attempt
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('email', $request->email)->get()->first();
        if (!$user) {
            return response()->json(['message' => 'User does not exist!'], 404);
        }
        $loginAttempt = Loginattempt::where('userId', $user->userId)->orderByDesc('created_at')
            ->limit(4)->get();
        if (count($loginAttempt) >= 3) {
            $thirdAttempt = $loginAttempt->last();
            if (Carbon::parse($thirdAttempt->created_at)->diffInMinutes(Carbon::now()) < 15) {
                return response()->json(['message' => 'You can not attempt login until after 15 minutes'], 403);
            }
        }
        return $next($request);
    }
}
