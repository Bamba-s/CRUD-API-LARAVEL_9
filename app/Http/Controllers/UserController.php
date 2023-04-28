<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // METHODE POUR INSCRIRE UN UTILISATEUR
    public function register(Request $request){
        // // Valider les données utilisateurs
        // $userData = $request ->validate([
        //     "name" => ["required","string","min:2","max:100"],
        //     "email" => ["required","email", "unique:users,email"],
        //     "password" => ["required","string", "min:8","max:100","confirmed"]
        // ]);

        //  // Créer un nouvel utilisateur
        // $users = User::create([
        //   "name" => $userData["name"],
        //   "email" => $userData["email"],
        //   "password" => bcrypt($userData["password"]),
        // ]);

        //  return response($users,201);

        // Valider les données utilisateurs
        $userData = $request ->validate([
            "name" => ["required","string","min:2","max:100"],
            "email" => ["required","email", "unique:users,email"],
            "password" => ["required","string", "min:8","max:100"]
        ], [
            'email.unique' => 'Un utilisateur existe déjà avec cet email'
        ]);

        // Utiliser une transaction pour éviter les enregistrements en double
        DB::beginTransaction();
        try {
            // Créer un nouvel utilisateur
            $users = User::create([
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => bcrypt($userData["password"]),
            ]);

            DB::commit();
            return response($users,201);

        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Une erreur s\'est produite lors de la création de l\'utilisateur.'], 500);
        }
    }

    /********METHODE POUR AUTHENTIFIER UN UTILISATEUR*******/
    public function login(Request $request){
        // Valider les données utilisateurs
        $userData = $request ->validate([
            "email" => ["required","email"],
            "password" => ["required","string", "min:8","max:100"]
        ]);
        $user = User::where("email",$userData["email"])->first();
        if(!$user) return response(["message"=>"Aucun utilisateur trouvé avec le mail: $userData[email]",401]);
        if(!Hash::check($userData["password"], $user->password)){
            return response(["message"=>"Mot de pas incorrect !",401]);
        }
        // Créer un token de connexion
        $token = $user->createToken("CLE_SECRETE")->plainTextToken;
        return response([
            "Utilisateur" => $user,
            "token" => $token
        ],200);
        // return $user;

    }
       // METHODE POUR LISTER TOUS LES UTILISATEURS
            public function listUsers()
        {
            $users = User::all();
            return response($users, 200);
        }
}
