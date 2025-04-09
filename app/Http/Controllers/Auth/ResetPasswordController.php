<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

            // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => "Aucun compte associé à cet email."], 404);
        }

        // Générer un code à 6 chiffres
        $code = mt_rand(10000, 99999);

        // Sauvegarder le code dans la base de données
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        // Envoyer le code par email
        Mail::raw("Bonjour ". $user->firstname . ",\n\n Votre code de réinitialisation sur la plateforme denommée MPC est : $code \nCe code expire dans 10 minutes.", function ($message) use ($request) {
            $message->to($request->email)->subject('Réinitialisation du mot de passe sur RememberMe');
        });

        return response()->json(['message' => "Un code de réinitialisation a été envoyé à $request->email : $code"], 200);

        
    }

    //  Étape 2 : Vérifier le code
    public function verifyResetCode(Request $request)
    {
        $request->validate([
             'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric:5'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
            
        
            

        if (!$record || !Hash::check($request->code, $record->token)) {
            return response()->json(['message' => 'Code invalide.'], 400);
        }

        // Convertir created_at en instance de Carbon
        $createdAt = Carbon::parse($record->created_at);

        // Vérifier si le token a expiré après 1 minute
        if ($createdAt->diffInMinutes(now()) > 1) {  // Le token expire après 12 minutes
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Le token a expiré.'], 400);
        }

        // Générer un token unique pour la réinitialisation
        $resetToken = Str::random(60);

        return response()->json(['reset_token' => $resetToken], 200);
    }

    //  Étape 3 : Mettre à jour le mot de passe
    public function resetPassword(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ],);
       
        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => "Aucun compte associé à cet email."], 404);
        }

        // Récupérer le token
    $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

    if (!$record) {
        return response()->json(['message' => 'Aucun code de réinitialisation trouvé.'], 404);
    }

    // Vérifier expiration
    if (Carbon::parse($record->created_at)->addMinutes(1)->isPast()) {
        DB::table('password_reset_tokens')->where('email', $request->email)->delete(); // Nettoyage
        return response()->json(['message' => 'Le code a expiré. Veuillez en demander un nouveau.'], 400);
    }

    if (!Hash::check($request->code, $record->token)) {
        return response()->json(['message' => 'Code invalide.'], 400);
    }




        $user->update(['password' => Hash::make($request->password)]);

        // Supprimer les codes de réinitialisation après l'utilisation
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mot de passe mis a jour avec succes.'], 200);
    }
}
