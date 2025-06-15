<?php

namespace App\Http\Controllers;

use App\Http\Response\ApiResponse;
use App\Models\MntPersonalInformationUserModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //  LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::user();
        $userInformation = MntPersonalInformationUserModel::where('user_id', $user->id)->first();

        $customClaims = [
            'user_id' => $user->id,
            'email' => $user->email,
            'userInformation' => $userInformation ? $userInformation->toArray() : null,
            'role' => $user->getRoleNames(),
        ];

        // Generar nuevo token 
        $token = JWTAuth::claims($customClaims)->fromUser($user);

        return response()->json([
            'token' => $token,
            'role' => $user->getRoleNames(),
        ], 200);
    }

    // para registrar
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|string|min:8|same:password',
            ], [
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'El correo no tiene un formato válido.',
                'email.unique' => 'El correo ya está en uso.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password_confirmation.same' => 'Las contraseñas no coinciden.',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 400);
            }

            $user = User::create([
                'name' => explode('@', $request->email)[0] . Str::random(3),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('User');

            DB::commit();

            // Autologin tras registro
            $credentials = $request->only('email', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'No se pudo iniciar sesión automáticamente'], 401);
            }

            return response()->json([
                'message' => 'Usuario creado y autenticado',
                'token' => $token,
                'role' => $user->getRoleNames(),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al crear el usuario: ' . $e->getMessage(), 500);
        }
    }

    //  Refrescar TOKEN
    public function refresh(Request $request)
    {
        try {
            $currentToken = JWTAuth::getToken();

            if (!$currentToken) {
                return response()->json(['error' => 'Token no proporcionado'], 400);
            }

            try {
                $user = JWTAuth::toUser($currentToken);
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                $currentToken = JWTAuth::refresh($currentToken);
                $user = JWTAuth::toUser($currentToken);
            }

            $userInformation = MntPersonalInformationUserModel::where('user_id', $user->id)->first();

            $customClaims = [
                'user_id' => $user->id,
                'email' => $user->email,
                'userInformation' => $userInformation ? $userInformation->toArray() : null,
                'role' => $user->getRoleNames()
            ];

            $newToken = JWTAuth::claims($customClaims)->fromUser($user);

            return response()->json([
                'token' => $newToken,
                'message' => 'Token actualizado',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //  LOGOUT
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'message' => 'No token provided',
                    'status' => 400,
                ], 400);
            }

            $user = JWTAuth::authenticate($token);
            $user->update(['is_logged_in' => false]); 

            return response()->json([
                'message' => 'Sesión cerrada correctamente',
                'status' => 200,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
