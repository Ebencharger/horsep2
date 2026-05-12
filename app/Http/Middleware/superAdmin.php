<?php

namespace App\Http\Middleware;

use App\Events\SystemNotification;
use App\Models\Role;
use App\Models\Userrole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class superAdmin
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = Auth::user();
            $superAdmin = Userrole::where('userId', $user->userId)->get()->first();
            if ($user && !$superAdmin) {
                return response()->json(["status" => false, 'message' => 'You are not permitted'], 403);
            }
            $operationId = $request->operationId;
            if (!$operationId) {
                return response()->json(["status" => false, 'message' => 'Operation ID is required'], 400);
            }
            // Check if this role has permission for the requested operationId
            $hasPermission = Role::join('permissions', 'roles.permissionId', '=', 'permissions.permissionId')
                ->where('roles.roleId', $superAdmin->roleId)
                ->where('permissions.permission', $operationId)
                ->exists();

            if (!$hasPermission) {
                return response()->json(["status" => false, 'message' => 'Permission denied'], 403);
            }
            return $next($request);
        } catch (\Throwable $th) {
        }
    }
}
