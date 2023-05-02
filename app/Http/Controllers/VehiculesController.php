<?php

namespace App\Http\Controllers;

use App\Models\Vehicules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehiculesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     /*********AFFICHER TOUS LES VEHICULES******** */
    public function index()
    {
        //
        $cars = Vehicules::all();
        if (count($cars) <= 0) {
            return response(['message' => 'Aucun véhicule trouvé !'], 200);
        }
        return response($cars, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**********ENREGISTRER LES VEHICULES DANS LA BASE DE DONNEES************** */
    public function store(Request $request)
    {
        // Validation des données du véhicules
        $validatedData = $request->validate([
            'marque' => 'string',
            'modele' => 'required|string',
            'prix' => 'required|numeric',
            'description' => 'string|min:5|max:255',
            'image' => 'required|mimes:png,jpeg,jpg,svg|max:4096',
            'user_id' => 'required|numeric'
        ]);
       // Vérifier si le véhicule existe
        $existingCar = Vehicules::where([
            'marque' => $validatedData['marque'],
            'modele' => $validatedData['modele'],
            'prix' => $validatedData['prix'],
            'description' => $validatedData['description'],
            'user_id' => $validatedData['user_id']
        ])->first();
     // Si le véhicule existe déjà, renvoyer une réponse d'erreur
        if ($existingCar) {
            return response(['message' => 'Ce véhicule existe déjà.'], 409);
        }
        // Stocker limage dans "public/images"
        $path = $request->file('image')->storePublicly('images', 'public');

         // Créer un nouveau répertoire si "public/images" n'existe pas déjà
        if (!Storage::disk('public')->exists('images')) {
            Storage::disk('public')->makeDirectory('images');
        }
    
        // Créer un nouveau véhicule en utilisant les données validées
        $car = new Vehicules($validatedData);
        $car->image = $path;
        $car->save();
    
        return response(['message' => 'Véhicule ajouté avec succès !']);
    }
    
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicules  $vehicules
     * @return \Illuminate\Http\Response
     */
     /********RECUPERER LES VEHICULES DANS LA BASE DE DONNEES******* */
    public function show($id)
    {
        /**RECUPERER UN VEHICULE* */
        // return Vehicules::find( $id )->first();
        
        //RECUPER VEHICULE AVEC LES INFORMATIONs DU USER L'AYANT CREE
         /**METHODE CLASSIQUE (Beginner) * */
        // $car = DB::table('vehicules')
        // ->join('users', 'vehicules.user_id', '=', 'users.id')
        // ->select('vehicules.*', 'users.name', 'users.email')
        // ->where('vehicules.id', '=', $id)
        // ->get()
        // ->first();
        // return $car;

        /**METHODE EN UTILISANT LE MODELE Eloquent (avanced) * */
        $vehicule = Vehicules::with('user')->find($id);

        if (!$vehicule) {
            return response()->json(['message' => "Aucun véhicule trouvé avec ID:$id"], 404);
        }
    
        return $vehicule;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehicules  $vehicules
     * @return \Illuminate\Http\Response
     */

    public function edit(Vehicules $vehicules)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicules  $vehicules
     * @return \Illuminate\Http\Response
     */

     /**********METTRE A JOURUN VEHICULE DANS LA BASE DE DONNEES*********** */
    public function update(Request $request,$id)
    {
         // Valider les données du véhicule
         $carsValidation = $request->validate([
            'marque' => ['string'],
            'modele' => ['string'],
            'prix' => ['numeric'],
            'description' => ['string', 'min:5', 'max:255'],
            'user_id' => ['required', 'numeric']
        ]);

        $car=Vehicules::find($id);
        if (!$car) {
            return response(['message' => "Aucun véhicule trouvé avec id:$id !"], 404);
        }
        if($car->user_id != $carsValidation['user_id']){
         return response(['message' => "Vous n'etes pas autorisé à modifier ce véhicule "], 403);
        }
        $car->update($carsValidation);
        return response(['message' => "Véhicule a été mise à jour avec succès ! "], 201);

        // // Mettre à jour les attributs du véhicule
        //     $car->marque = $request->input('marque', $car->marque);
        //     $car->modele = $request->input('modele', $car->modele);
        //     $car->prix = $request->input('prix', $car->prix);
        //     $car->description = $request->input('description', $car->description);
        //     $car->user_id = $request->input('user_id', $car->user_id);

        //     // Enregistrer les modifications
        //     $car->save();

        //     // Retourner une réponse avec le véhicule mis à jour
        //     return $car;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicules  $vehicules
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request,$id)
    {
                // Valider les données du véhicule
                $carsValidation = $request->validate([
                    'user_id' => ['required', 'numeric']
                ]);
        
        $car=Vehicules::find($id);
        if (!$car) {
            return response(['message' => "Aucun véhicule trouvé avec id:$id !"], 404);
        }
        if($car->user_id != $carsValidation['user_id']){
            return response(['message' => "Vous n'etes pas autorisé à supprimer ce véhicule "], 403);
           }
           $value=Vehicules::destroy($id);
           if(boolval($value)==false){
            return response(['message' => "Aucun véhicule trouvé avec id:$id !"], 404);
           }
           return response(['message' => " Véhicule supprimé !"], 200);     
    }
} 