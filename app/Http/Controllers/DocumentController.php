<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Log\NullLogger;

class DocumentController extends Controller
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
               'collection_id' => 'required|string|max:255',
               'photo' => 'required|file|mimes:jpg,jpeg|max:10240',
               
       
           ]);
           


           $path = $request->photo('photo')->store('documents', 'public');

           //  Création de la collection
           $collection = Document::create([
               //'id' => Str::uuid(), 
               'user_id' => $user->id, // Récupérer l'utilisateur connecté
               'collection_id' => $request->collection_id,
               'nom' => $request->nom,
               'photo' => $path,
       
           ]);

           //  Retourner une réponse JSON
           return response()->json([
               'message' => 'Document créé avec succès',
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
    public function rename($id, Request $request)
    {
        $document = Document::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $document->update(['nom' => $request->nom]);

        return response()->json(['message' => 'Nom du document mis à jour', 'document' => $document]);
    }

}
