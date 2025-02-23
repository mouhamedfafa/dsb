<?php

namespace App\Http\Controllers;

use App\Models\User;
use PharIo\Manifest\Email;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


class AuthController extends Controller
{

    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
        return response(['message'=>'données invalides'],status:Response::HTTP_UNAUTHORIZED);
        }

    $user = Auth::user();
    $token = $user->createToken('token')->plainTextToken;
    $cookie =cookie('jwt', value: $token,minutes:60*24); //1 jour

    return response([
        'message'=>$token,

    ])->withCookie($cookie	);
}

public function createUser(Request $request){
    $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'number' => 'sometimes|required|max:20',
        'adresse' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|required|unique:users,email,',
            'profils_id' => 'sometimes|integer|exists:profils,id',
            'password' => 'sometimes|string|min:8|confirmed',
            'profile_photo_path' => 'sometimes|string|max:255',
        ]);

        // dd($request);
        $user = User::create([
            "name"=> $request->input('name'),
            "number"=> $request->input("number"),
            "adresse"=> $request->input("adresse"),
            "email"=> $request->input("email"),
            "profils_id"=> $request->input("profils_id"),
             "password"=> Hash::make($request->input("password")),
            "profile_photo_path"=> $request->profile_photo_path,
        ]);


    }

    public function update(Request $request): RedirectResponse
    {
        // Validate the new password length...

        $request->user()->fill([

            'password' => Hash::make($request->newPassword)
        ])->save();

        return redirect('/profile');
    }
    public function updateUser(Request $request, $id)
{
    // Trouver l'utilisateur par son ID
    $user = User::find($id);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    // Valider les données de la requête
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'number' => 'sometimes|string|max:20',
        'adresse' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $user->id,
        'profils_id' => 'sometimes|integer|exists:profils,id',
        'password' => 'sometimes|string|min:8|confirmed',
        'profile_photo_path' => 'sometimes|string|max:255',
    ]);

    // Mettre à jour les champs modifiés
    $user->update([
        'name' => $request->input('name', $user->name),
        'number' => $request->input('number', $user->number),
        'adresse' => $request->input('adresse', $user->adresse),
        'email' => $request->input('email', $user->email),
        'profils_id' => $request->input('profils_id', $user->profils_id),
        'password' => $request->filled('password') ? Hash::make($request->input('password')) : $user->password,
        'profile_photo_path' => $request->input('profile_photo_path', $user->profile_photo_path),
    ]);

    // Retourner une réponse avec l'utilisateur mis à jour
    return response()->json(['message' => 'Utilisateur mis à jour avec succès', 'user' => $user]);
}

    public function listusers(){

        // dd("ok");
        return User::all();
    }

public function listuser($id){
    // dd(User::findOrFail( $id));
    return User::findOrFail( $id );
}

public function deleteUser($id)
{
    // Trouver l'utilisateur par son ID
    $user = User::find($id);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    // Supprimer l'utilisateur
    $user->delete();

    // Retourner une réponse
    return response()->json(['message' => 'Utilisateur supprimé avec succès']);
}



public function logout(Request $request)
{
    // Révoquer le token actuel
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Déconnexion réussie',
    ], 200);
}

}



















