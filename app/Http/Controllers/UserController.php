<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\FieldValidationRuleEnum;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        // Search for the ID from the User
        $user = Auth::user();
        // Return page back with the variable
        return view('users.settings', ['user' => $user]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Search for the ID from the User
        $user = Auth::user();

        // Validate data
        $request->validate([
            'name' => 'required',
            'email' => 'required', 'unique:users, email',
            'phone_number' => ['required', FieldValidationRuleEnum::PHONE_NUMBER],
        ]);

        

        // Set the data from the form in a request
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        // Save the data into the database
        $user->save();

        // Redirect back to the page and give a status with it
        return redirect()->back()->with('message', __('app_messages.user.update.success'));
    }
}
