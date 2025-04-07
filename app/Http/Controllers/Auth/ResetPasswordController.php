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
        $code = mt_rand(100000, 999999);

        // Sauvegarder le code dans la base de données
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $code, 'created_at' => now()]
        );

        // Envoyer le code par email
        Mail::raw("Bonjour ". $user->firstname . " Votre code de réinitialisation sur la plateforme denommée MPC est : $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Réinitialisation du mot de passe sur RememberMe');
        });

        return response()->json(['message' => "Un code de réinitialisation a été envoyé à $request->email : $code"], 200);

        
    }

    //  Étape 2 : Vérifier le code
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            // 'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric'
        ]);

        $record = DB::table('password_reset_tokens')
            // ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();
            
        
            

        if (!$record) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 400);
        }

        // Convertir created_at en instance de Carbon
    $createdAt = Carbon::parse($record->created_at);

    // Vérifier si le token a expiré après 1 minute
    if ($createdAt->diffInMinutes(now()) > 30) {  // Le token expire après 30 minutes
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
            // 'email' => 'required|email|exists:users,email',
            // 'reset_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ],);
       
        

        
        

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => "Aucun compte associé à cet email."], 404);
        }
        $user->update(['password' => Hash::make($request->password)]);

        // Supprimer les codes de réinitialisation après l'utilisation
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mot de passe mis a jour avec succes.'], 200);
    }
}
