<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class HomeController extends Controller implements HasMiddleware
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function __invoke()
    {

        //Obtener a quien seguimos
        $ids = Auth::user()->followings->pluck('id')->toArray();
        $posts = Post::whereIn('user_id', $ids)->latest()->paginate(10);

        return view('home', [
            'posts' => $posts
        ]);   
    }
}
