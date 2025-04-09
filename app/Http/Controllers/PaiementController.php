<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;

class PaiementController extends Controller
{
    public function index()
{
    // Récupère tous les paiements liés à l'utilisateur connecté
    $paiements = Paiement::where('utilisateur_id', auth()->id())->get();

    return response()->json([
        'paiements' => $paiements,
    ]);
}




    // Enregistrement du paiement après succès sur Kkia Pay
    public function enregistrer(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            //'reference' => 'required|string|unique:paiements,reference', // si Kkia Pay renvoie une référence
        ]);

        $paiement = Paiement::create([
            'montant' => $request->montant,
            'date_paiement' => now(),
           // 'reference' => $request->reference, // optionnel si disponible
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Paiement enregistré avec succès',
            'paiement' => $paiement,
        ], 201);
    }
}
