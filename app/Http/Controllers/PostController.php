<?php


namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class PostController extends Controller implements HasMiddleware

{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['show', 'index']),
        ];
    }

   
    
    public function index(User $user)
    {
        $posts = Post::where('user_id', $user->id)->latest()->paginate(10);

        return view('dashboard', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

   public function create()
   {
        return view('posts.create');
   }
   public function store(Request $request)
   {
        $validated = $request->validate([
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'imagen' => 'required'

        ]);

        Post::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
            'user_id' => Auth::user()->id
        ]);

        return redirect()->route('posts.index', Auth::user()->username);
    }

    public function show(User $user, Post $post)
    {
        return view('posts.show', [
            'post' => $post,
            'user' => $user
        ]);
    }

    public function destroy(Post $post)
    {
      if (!Gate::allows('delete', $post)) {
        abort(403);
      }

      $post->delete();

      // Elminar imagen del upload
      $imagen_path = public_path('uploads/'. $post->imagen );

      if (File::exists($imagen_path)) {
        
        unlink($imagen_path);
      }

      return redirect()->route('posts.index', Auth::user()->username);

    }
}
