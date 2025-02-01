<?php
 
namespace App\Http\Controllers;
 
 
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

 
 
 
class ImagenController extends Controller
{
    //
    public function store(Request $request)
    {

            // Validar la imagen
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Obtener el archivo de la solicitud
        $imagen = $request->file('file');

        // Generar un nombre único para la imagen
        $nombreImagen = Str::uuid() . '.' . $imagen->extension();

        // Crear un ImageManager con el driver GD
        $manager = new ImageManager(new Driver());

        // Leer la imagen desde el archivo cargado
        $image = $manager->read($imagen->getPathname());

        // Redimensionar la imagen proporcionalmente a un ancho de 1000px
        $image->resize(1000, 1000);

        // Ruta donde se guardará la imagen
        $directorio = public_path('uploads');

        // Crear la carpeta si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $imagenesPath = $directorio . '/' . $nombreImagen;

        // Guardar la imagen en el directorio especificado
        $image->save($imagenesPath);

        // Retornar el nombre de la imagen
        return response()->json(['imagen' => $nombreImagen]);
        
    }
}


