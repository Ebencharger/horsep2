<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class setOperationId
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        $operationId = match ($routeName) {
            'createUser' => 'createUser',
            'login' => 'login',
            'sendResetPasswordOtp' => 'sendResetPasswordOtp',
            'confirmOtp' => 'confirmOtp',
            'resetPassword' => 'resetPassword',
            'logoutSuspicious' => 'logoutSuspicious',
            'updateUser' => 'updateUser',
            'getUser' => 'getUser',
            'editUserPicture' => 'editUserPicture',
            'deleteUser' => 'deleteUser',
            'singleUser' => 'singleUser',
            'verifyEmail' => 'verifyEmail',
            'addRolesToUser' => 'addRolesToUser',
            'getUserRoles' => 'getUserRoles',
            'removeUserRoles' => 'removeUserRoles',
            'logout' => 'logout',
            'refresh' => 'refresh',

            'createPermission' => 'createPermission',
            'updatePermission' => 'updatePermission',
            'getPermission' => 'getPermission',
            'getSinglePermission' => 'getSinglePermission',
            'createRole' => 'createRole',
            'getRole' => 'getRole',
            'updateRole' => 'updateRole',
            'getSingleRole' => 'getSingleRole',


            'adminInviteUserForARole' => 'adminInviteUserForARole',


            'checkInvitationValidity' => 'checkInvitationValidity',
            'InviteeSignup' => 'InviteeSignup',


            'set2fa' => 'set2fa',

            'resendOtp' => 'resendOtp',

            'getNotification' => 'getNotification',
            'ReadNotification' => 'ReadNotification',



            default => null
        };
        if ($operationId) {
            $request->merge(['operationId' => $operationId]);
        }
        return $next($request);
    }
}
