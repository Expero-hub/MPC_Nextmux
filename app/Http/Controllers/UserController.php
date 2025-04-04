<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Méthode pour afficher le profil de l'utilisateur
    public function profile()
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Retourner les informations du profil de l'utilisateur
        return response()->json([
            "id" => $user->id,
            "lastname" =>$user->lastname,
            "firstname" =>$user->firstname,
            "telephone" =>$user->telephone,
            "photo" =>$user->photo,
            "email" => $user->email]);
    }
}
