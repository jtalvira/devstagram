<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    //
    public function store(Request $request, User $user, Post $post) {
      
        //validar
        $validated = $request->validate([
            'comentario' => 'required|max:255'
        ]);

        //Almacenar a base de datos
        Comentario::create([
            'user_id' => Auth::user()->id,
            'post_id' => $post->id,
            'comentario' => $request->comentario
        ]);

        // imprimir resultado
        return back()->with('mensaje', 'Comentario Realizado Correctamente');

    }
}
