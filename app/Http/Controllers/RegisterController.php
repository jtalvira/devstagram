<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //
    public function index () 
    {
        return view('auth.register');
    }

    public function store(Request $request) 
    {
        //dd($request->get('name'));

        // Modificar Request

        $request->request->add(['username' => Str::slug($request->username)]);

        // Validación

        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users|min:3|max:20',
            'email' => 'required|unique:users|email|max:60',
            'password' => 'required|confirmed|min:6'

        ]);

        User::create([
            'name' => $request->name,
            'username' => $request ->username,
            'email' => $request->email,
            'password' => $request->password
        ]);

        // Autenticar usuarios

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        
        // redireccionar
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->route('posts.index', Auth::user()->username);
        }

        return back()->withErrors([
            'email' => 'Estas credenciales no existen'
        ]);
        
    }
}
