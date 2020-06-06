<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Clientes;

class ClientesController extends Controller
{
    public function index()
    {

        $json = array(
            "detalle" => "Pepito no encontradoooo"
        );
        echo json_encode($json, true);
    }

    /** Crear un registro **/

    public function store(Request $request)
    {

        //recoger datos
        $datos = array(
            "primer_nombre" => $request->input("primer_nombre"),
            "primer_apellido" => $request->input("primer_apellido"),
            "email" => $request->input("email")
        );


        /** Se valida la informacion recibida en el request*/

        $validator = Validator::make($datos, [
            'primer_nombre' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clientes'

        ]);

        /**Si falla Se enviara un json informando del error */
        if ($validator->fails()) {
            $json = array(
                "detalle" => "Datos no validos",
                "status" => "404"
            );
            return json_encode($json, true);
        } else {

            /**Se crean las encriptacion */
            $id_cliente = Hash::make($datos['primer_nombre'] . $datos['primer_apellido'] . $datos['email']);
            $llave_secreta = Hash::make($datos['email'] . $datos['primer_apellido'] . $datos['primer_nombre']);

            /**Se crea el modelo */
            $cliente = new Clientes();

            /**La data recolectada se envia al modelo y se guarda */
            $cliente->primer_nombre = $datos['primer_nombre'];
            $cliente->primer_apellido = $datos['primer_apellido'];
            $cliente->email = $datos['email'];
            $cliente->id_cliente = $id_cliente;
            $cliente->llave_secreta = $llave_secreta;

            $cliente->save();


            /**Se envia al cliente la notifacion que el proceso fue exitoso */
            $json = array(
                "status"  => "200",
                "detalle" => $id_cliente,
                "llave_secreta" => $llave_secreta
            );

            return json_encode($json, true);
        }
    }
}
