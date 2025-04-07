<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserprofileController extends Controller
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

    public function renameProfile(Request $request){
        try{
            $request->validate([
                'lastname'   => 'required|string|max:255',
                'firstname'  => 'required|string|max:255',
                'telephone'  => 'nullable|string|max:20',
                'photo'      => 'nullable|string', 
                'email'      => 'required|email|unique:users,email,' . Auth::id(),
                'password'   => 'nullable|string|min:6',
            ]);
        
            $user = User::findOrFail(Auth::id()); 
        
            $data = $request->only(['lastname', 'firstname', 'telephone', 'photo', 'email']);
        
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
        
            $user->update($data);
        
            return response()->json([
                'message' => 'Profil mis à jour avec succès.',
                'user' => $user,
            ]);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'erreur lors de la mise à jour de votre profil',
                'erreur' => $e->getMessage()
                 
            ], 404);
        }
    }
}
