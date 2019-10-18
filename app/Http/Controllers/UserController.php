<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;

class UserController extends Controller
{
    /* showAllUserInfo */
    public function show()
    {
        /* Search for the ID from the User */
        $user = Auth::user();
        /* Return page back with the variable */
        return view('users.settings', ['user' => $user]);
    }

    /* updateUserInfo */
    public function update(Request $request)
    {
        /* Validate data */ 
        $request->validate([
            'name' => 'required',
            'email' => 'required', 'unique:users, email,$this->id,id',
            'phone_number' => 'required', 'numeric',
        ]);
        /* Search for the ID from the User */
        $user = Auth::user();
        /* Set the data from the form in a request */
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        /* Save the data into the database */
        $user->save();
        /* Redirect back to the page and give a status with it */
        return redirect()->back()->with('message', 'Je hebt met succes je account instellingen gewijzigd!');
    }
}
