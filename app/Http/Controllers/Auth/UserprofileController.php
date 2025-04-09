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
            "photo_url" => asset($user->photo),
            "email" => $user->email]);
    }

    public function renameProfile(Request $request){
        try{
            $request->validate([
                'lastname'   => 'nullable|string|max:255',
                'firstname'  => 'nullable|string|max:255',
                'telephone'  => 'nullable|string|max:20',
                'photo'      => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'email'      => 'nullable|email|unique:users,email,' . Auth::id(),
                'password'   => 'nullable|string|min:6',
            ]);
        
            $user = User::findOrFail(Auth::id()); 

            $data = $request->only(['lastname', 'firstname', 'telephone',  'email']);
        
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }


            //Traiter l'image
            if($request->hasFile('photo')){
                //Récupération du fichier
                $file = $request->file('photo');
                //Générer un nom unique pour l'image
                $imageName = $file->getClientOriginalName();

                // Stockage dans storage/app/public/images
                if($user->photo && file_exists(public_path($user->photo))){
                    unlink(public_path($user->photo));
                }

                $path = $file->storeAs('images', $imageName, 'public');

                $data['photo'] = 'storage/' . $path;

            }

           
            logger($data);
        
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
