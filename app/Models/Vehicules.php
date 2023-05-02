<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicules extends Model
{
    use HasFactory;

    protected $fillable =["marque","modele", "prix", "description","image", "user_id"];
 
    /****DEFINIR LA RELATION ENTRE 'véhicules' et 'user' SELON LE MODELE Eloquent (show->VehiculeController.php) * */
    protected $table = 'vehicules';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
