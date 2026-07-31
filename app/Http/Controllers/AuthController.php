<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Whatsapp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Page connexion / inscription. Si l'utilisateur est déjà connecté, on le
     * renvoie vers son compte (ou la page qu'il voulait atteindre).
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->intended(route('compte.index'));
        }

        return view('auth.login');
    }

    /**
     * Authentifie un client existant puis reprend le parcours d'achat là où il
     * s'était arrêté (redirect()->intended : panier/checkout mémorisé).
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $identifiants = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($identifiants, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects. Vérifiez votre e-mail et votre mot de passe.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('compte.index'));
    }

    /**
     * Crée un compte client puis connecte automatiquement et reprend le
     * parcours d'achat.
     */
    public function register(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Nettoyage + normalisation du numéro (+225XXXXXXXXXX), puis unicité.
        $whatsapp = Whatsapp::normaliser($donnees['whatsapp']);
        if ($whatsapp === null) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Numéro WhatsApp invalide. Exemple : 0556400246 ou +2250556400246.',
            ]);
        }
        if (User::where('whatsapp', $whatsapp)->exists()) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Ce numéro WhatsApp est déjà associé à un compte.',
            ]);
        }

        $user = User::create([
            'name' => $donnees['name'],
            'email' => $donnees['email'],
            'whatsapp' => $whatsapp,
            'password' => Hash::make($donnees['password']),
            'role' => User::ROLE_CLIENT,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('compte.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
