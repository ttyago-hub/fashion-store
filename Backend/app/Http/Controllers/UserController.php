<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Listar todos los usuarios
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Cambiar el rol de un usuario
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Rol actualizado correctamente',
            'user' => $user
        ]);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }
}
