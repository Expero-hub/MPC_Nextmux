<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{
    // Enregistrement de l'utilisateur
    public function register(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'photo' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Création de l'utilisateur
        $user = User::create([
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'telephone' => $request->telephone,
            'photo' => $request->photo,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Retourner les informations de l'utilisateur
        return response()->json([
            'Nom Utiliateur' => $user->lastname,
            'Contact' => $user->telephone,
            'Adresse_Email' => $user->email,
            'message' => 'Utilisateur créé avec succès!'
        ], 201);
    }

    // Connexion de l'utilisateur et génération du token
    public function login(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 400);
        }

        // Vérification de l'email et mot de passe
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Création du token d'accès
        $token = $user->createToken('API Token')->accessToken;

        // Retourner le token d'accès
        return response()->json([
            'token' => $token
            
        ]);
    }
    // bcrypt($token)

    public function logout(Request $request) {
        $user = $request->user();
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
    
        // Révocation de chaque token d'accès et de son refresh token associé
        $user->tokens->each(function ($token) use ($tokenRepository, $refreshTokenRepository) {
            $tokenRepository->revokeAccessToken($token->id);
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
        });
    
        return response()->json("Vous êtes déconnecté !");
    }
}
