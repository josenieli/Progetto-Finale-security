<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $user->update($request->all()); // VULNERABILE: mass assignment

        return redirect()->route('profile.edit')->with('message', 'Profilo aggiornato');
    }
}