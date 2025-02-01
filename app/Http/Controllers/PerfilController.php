<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PerfilController extends Controller implements HasMiddleware
{
    //

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
    

    public function index()
    {
       return view('perfil.index');
    }

    public function store(Request $request)
    {

        // Modificar Request
        $request->request->add(['username' => Str::slug($request->username)]);

        $validated = $request->validate([
            // 'username' => 'required|unique:users|min:3|max:20'
            'username' => [
                'required', 
                // "unique:users,username,{auth()->user()->username}", 
                Rule::unique('users', 'username')->ignore(Auth::user()),
                'min:3', 
                'max:20', 
                'not_in:twitter,edita-perfil'
                ]
        ]);


        if($request->imagen) 
        {
             // Obtener el archivo de la solicitud
            $imagen = $request->file('imagen');

            // Generar un nombre único para la imagen
            $nombreImagen = Str::uuid() . '.' . $imagen->extension();

            // Crear un ImageManager con el driver GD
            $manager = new ImageManager(new Driver());

            // Leer la imagen desde el archivo cargado
            $image = $manager->read($imagen->getPathname());

            // Redimensionar la imagen proporcionalmente a un ancho de 1000px
            $image->resize(1000, 1000);

            // Ruta donde se guardará la imagen
            $directorio = public_path('perfiles');

            // Crear la carpeta si no existe
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            $imagenesPath = $directorio . '/' . $nombreImagen;

            // Guardar la imagen en el directorio especificado
            $image->save($imagenesPath);

            }

        // Guardar Cambios
        $usuario = User::find(Auth::user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? Auth::user()->imagen ?? '';
        $usuario->save();

        // Redireccionar

        return redirect()->route('posts.index', $usuario->username);


    }
}
