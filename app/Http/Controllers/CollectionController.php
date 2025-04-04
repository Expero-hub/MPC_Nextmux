<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

             // Vérifier si l'utilisateur est authentifié
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

    
             //  Validation des données
            $request->validate([
                'nom' => 'required|string|max:255',
        
            ]);
            

            //  Création de la collection
            $collection = Collection::create([
                //'id' => Str::uuid(), 
                'user_id' => $user->id, // Récupérer l'utilisateur connecté
                'nom' => $request->nom,
        
            ]);
            
            


            //  Retourner une réponse JSON
            return response()->json([
                'message' => 'Collection créée avec succès',
                'collection' => $collection
                ], 201);
        }
        catch(\Exception $e){

            return response()->json([
                "message" => "Une erreur est survenue lors de lenregistrement ",
                "erreur" => $e->getMessage()

            ], 500);

        }
       
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
