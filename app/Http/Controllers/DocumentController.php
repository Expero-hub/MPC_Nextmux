<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       try{
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)
                         ->where('etat', 'actif')
                         ->latest()
                         ->get();

    return response()->json([
        
        'title' => 'DOCUMENTS',
        'documents' => $documents,
    ]); 
       }catch(\Exception $e){

        return response()->json([
            "message" => "Une erreur est survenue lors de la récupération ",
            "erreur" => $e->getMessage()

        ], 500);

    }
    }

    //affichage de la corbeille 
    public function corbeille()
{
    $user = Auth::user();
        $documents = Document::where('user_id', $user->id)
                         ->where('etat', 'corbeille')
                         ->latest() 
                         ->get();

    return response()->json([
        'status' => 'success',
        'title' => 'CORBEILLE',
        'documents' => $documents,
    ]);
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
           

           //Traiter l'image

           if($request->hasFile('photo')){
            //Récupération du fichier
            $file = $request->file('photo');
            //Générer un nom unique pour l'image
            $imageName = time() . '_' .$file->getClientOriginalName();

            // Stockage dans storage/app/public/documents

            $path = $file->storeAs('documents', $imageName, 'public');

           }
           else{
            return response()->json([
                'message' => 'fichier introuvable'
            ], 422);
           }

           $path = $request->photo('photo')->store('documents', 'public');


           //  Création du document
           $collection = Document::create([
               //'id' => Str::uuid(), 
               'user_id' => $user->id, // Récupérer l'utilisateur connecté
               'collection_id' => $request->collection_id,
               'nom' => $request->nom,

               'photo' => 'storage/' . $path,

       
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



           //  Renomer
           $document = Document::findOrFail($id);
           $document->update($request->all());
               
               
           //  Retourner une réponse JSON
           return response()->json([
               'message' => 'Document '.$document->nom .' renomé',
               'collection' => $document
               ], 201);
       }
       catch(\Exception $e){

           return response()->json([
               "message" => "Une erreur est survenue lors de la modification ",
               "erreur" => $e->getMessage()

           ], 500);

       }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function placerCorbeille(Request $request, $id)
{
    $request->validate([
        'password' => 'required|string',
    ]);

    try{
        $user = Auth::user();

         // Vérification du mot de passe
         if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect.'
            ], 403);
        }
        $document = Document::where('id', $id)
                        ->where('user_id', $user->id)
                        ->where('etat', 'actif')
                        ->first();

        if (!$document) {
        return response()->json(['message' => 'Document introuvable ou déjà en corbeille'], 404);
        }

    $document->update([
        'etat' => 'corbeille',
       
        ]);

        return response()->json(['message' => 'Document déplacé dans la corbeille']);
    }catch(\Exception $e){
        return response()->json([
            'message' => 'Une erreur est survenue',
            'erreur' => $e->getMessage()
             
        ], 500);
    }
}

//restaurer un doc
public function restaurer($id)
{
    try{
        $user = Auth::user();
    $document = Document::where('id', $id)
                        ->where('user_id', $user->id)
                        ->where('etat', 'corbeille')
                        ->first();

    if (!$document) {
        return response()->json(['message' => 'Document introuvable ou déjà restauré'], 404);
    }

    $document->update([
        'etat' => 'actif',
        
    ]);

    return response()->json(['message' => 'Document restauré avec succès']);
    }catch(\Exception $e){
        return response()->json([
            'message' => 'Document introuvable ou déjà',
            'erreur' => $e->getMessage()
             
        ], 404);
    }
}




    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        try{
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)){
                return response()->json([
                    'message' => 'Mot de passe incorrect'
                ]);
            }
           
            $document = Document::where('id', $id)
                                ->where('user_id', $user->id)
                                ->where('etat', 'corbeille')
                                ->first();
        
            if (!$document) {
                return response()->json(['message' => 'Document introuvable ou déjà restauré'], 404);
            }
        
            $document->delete();

            return response()->json(['message' => 'Document supprimé définitivement']);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Suppression échouée',
                'erreur' => $e->getMessage()
                 
            ], 404);
        }
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
