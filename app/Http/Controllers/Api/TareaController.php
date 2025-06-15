<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarea;
use Illuminate\Support\Facades\Auth;
 
class TareaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
        // Admin ve todas las tareas
        $tareas = Tarea::all();
        } else {
        // Usuarios normales solo ven las suyas
        $tareas = Tarea::where('user_id', $user->id)->get();
        }

        return response()->json(['data' => $tareas], 200);
    }

 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'completada' => 'boolean',
            'progreso' => 'nullable|integer|min:0|max:100' // ✅ validación añadida
        ]);

        $tarea = Auth::user()->tareas()->create($validated);

 
        return response()->json($tarea, 201);
    }
 
    public function show(string $id)
    {
        $tarea = Tarea::findOrFail($id);
 
        if ($tarea->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
 
        return response()->json($tarea);
    }
 
    public function update(Request $request, string $id)
    {
        $tarea = Tarea::findOrFail($id);
 
        if ($tarea->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
 
        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'completada' => 'boolean',
            'progreso' => 'nullable|integer|min:0|max:100' // ✅ validación añadida
        ]);

        $tarea->update($validated); // No necesitas el código manual si usas $fillable correctamente

 
        return response()->json($tarea);
    }
 
    public function destroy(string $id)
    {
        $tarea = Tarea::findOrFail($id);
 
        if ($tarea->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
 
        $tarea->delete();
 
        return response()->json(['mensaje' => 'Tarea eliminada'], 204);
    }
}