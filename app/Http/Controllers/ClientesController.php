<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(){

        $json = array(
            "detalle" => "Pepito no encontradoooo" 
        );
        echo json_encode($json, true);

    }
    
    /** Crear un registro **/

    public function store(Request $request){
        //recoger datros
        $datos = array("primer_nombre"=>$request->input("primer_nombre"),
        "primer_apellido"=>$request->input("primer_apellido"),
        "email"=>$request->input("email"));

        echo '<pre>'; print_r($datos); echo '</pre>';
    }
}
