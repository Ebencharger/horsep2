<?php

namespace App\Http\Controllers;

use App\Models\User as ModelsUser;
use App\Usertype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class User extends Controller
{
    public function createUser(Request $request)
    {
        $validator = Validator::make($request, [
            'firstName' => 'required|min:3|string',
            'lastName' => 'required|min:3|string',
            'userName' => 'required|min:3|string',
            'country' => 'required|min:3|string',
            'state' => 'required|min:3|string',
            'phoneNumber' => 'required|min:3|string',
            'town' => 'required|min:3|string',
            'email' => 'required|string|email|unique:users',
            'roleId' => 'required|string',
            'role' => ['required', 'string', Rule::enum(Usertype::class)],
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 500,
                'detail' => $validator->errors()->first()
            ], 500);
        }
       $role=
        ModelsUser::create([
            'role' => $request->role,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'userName' => $request->userName,
            'state' => $request->state,
            'phoneNumber' => $request->phoneNumber,
            'town' => $request->town,
            'userId' => v4(),
            'password' => Hash::make($request->password),
            'created_at' => now()
        ]);
    }
}
