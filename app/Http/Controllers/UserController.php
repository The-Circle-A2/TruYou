<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function list(Request $request) {

        $users = User::pluck('username');

        $this->log('[TRUYOU] Get users');

        return $this->returnResponse(response()->json($users));
    }

    public function get(Request $request, $username) {

        $user = User::where('username', $username)
            ->first();

        $this->log('[TRUYOU] Get user ' . $username);

        if($user == null) return $this->returnResponse(response()->json(['error' => 'User not found'], 404));

        return $this->returnResponse(response()->json($user));
    }

}
