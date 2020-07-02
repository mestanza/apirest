<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cursos;
use App\Clientes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    /**
     * Display a listing of the resource.
     * Recibo el header de authorization
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $token = $request->header('Authorization');

        $clientes = Clientes::all();

        foreach ($clientes as $key => $value) {

            if ('Basic ' . base64_encode($value['id_cliente'] . ':' . $value['llave_secreta']) == $token) {
                /**Anteriormente 
                 * $cursos = Cursos::all();
                */
            
                if(isset($_GET['page'])){
                    $cursos = Cursos::paginate(15);
                }else{
                    
                    $cursos = Cursos::all();
                }
                

                if (!empty($cursos)) {
                    $json = array(
                        "status"  => "200",
                        "Total registros" => count($cursos),
                        "detalles" => $cursos
                    );
                } else {
                    $json = array(
                        "status"  => "404",
                        "Total registros" => 0,
                        "detalle" => "No hay cursos"
                    );
                }
            } else {
                $json = array(
                    "detalle" => "Datos no hay autorizacion para estos datos",
                    "status" => "404"
                );
            }
        }

        return json_encode($json);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->header('Authorization');

        $clientes = Clientes::all();

        $json = array();


        foreach ($clientes as $key => $value) {

            if ('Basic ' . base64_encode($value['id_cliente'] . ':' . $value['llave_secreta']) == $token) {

                //recoger datos
                $datos = array(
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio")
                );

                if (!empty($datos)) {
                    $validator = Validator::make($datos, [
                        "titulo" => 'required|string|max:255:cursos',
                        "descripcion" => 'required|string|max:255:cursos',
                        "instructor" => 'required|string|max:255',
                        "imagen" => 'required|string|max:255:cursos',
                        "precio" => 'required|numeric'

                    ]);

                    if ($validator->fails()) {
                        $json = array(
                            "detalle" => "Los registros con errores",
                            "status" => "404"
                        );
                    } else {
                        $cursos = new Cursos();
                        $cursos->titulo = $datos['titulo'];
                        $cursos->descripcion = $datos['descripcion'];
                        $cursos->instructor = $datos['instructor'];
                        $cursos->imagen = $datos['imagen'];
                        $cursos->precio = $datos['precio'];
                        $cursos->id_cliente = $value['id'];

                        $cursos->save();

                        $json = array(
                            "detalle" => "Se ha guardado con exito",
                            "status" => "200"
                        );

                        return print_r($json, true);
                    }
                } else {
                    $json = array(
                        "detalle" => "Los registros no pueden estar vacios",
                        "status" => "404"
                    );
                }
            }
        }

        return json_encode($json, true);
    }

    /**
     * UN solo registro
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $token = $request->header('Authorization');

        $clientes = Clientes::all();

        $json = array();

        foreach ($clientes as $key => $value) {

            if ('Basic ' . base64_encode($value['id_cliente'] . ':' . $value['llave_secreta']) == $token) {

                $curso = Cursos::where('id', $id)->get();

                if (!empty($curso)) {
                    $json = array(
                        "status"  => "200",
                        "detalles" => $curso
                    );
                } else {
                    $json = array(
                        "status"  => "404",
                        "detalle" => "No hay curso"
                    );
                }
            } else {
                $json = array(
                    "detalle" => "Datos no hay autorizacion para estos datos",
                    "status" => "404"
                );
            }
        }
        return json_encode($json);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $token = $request->header('Authorization');

        $clientes = Clientes::all();

        $json = array();


        foreach ($clientes as $key => $value) {

            if ('Basic ' . base64_encode($value['id_cliente'] . ':' . $value['llave_secreta']) == $token) {

                //recoger datos
                $datos = array(
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio")
                );

                if (!empty($datos)) {
                    $validator = Validator::make($datos, [
                        "titulo" => 'required|string|max:255',
                        "descripcion" => 'required|string|max:255',
                        "instructor" => 'required|string|max:255',
                        "imagen" => 'required|string|max:255',
                        "precio" => 'required|numeric'

                    ]);

                    if ($validator->fails()) {
                        $json = array(
                            "detalle" => "Los registros con errores",
                            "status" => "404"
                        );
                    } else {

                        //trae curso

                        //id del curso
                        $traer_curso = Cursos::where("id", $id)->get();

                        //Si id del usuario es igual al id de quien creo el curso, se valida
                        if ($value["id"] == $traer_curso[0]['id_cliente']) {

                            $datos = array(
                                "titulo" => $datos["titulo"],
                                "descripcion" => $datos["descripcion"],
                                "instructor" => $datos["instructor"],
                                "imagen" => $datos["imagen"],
                                "precio" => $datos["precio"]
                            );

                            Cursos::where('id', $id)->update($datos);
                            $json = array(
                                "detalle" => "Se ha actualizado el curso con exito",
                                "status" => "200"
                            );

                            return print_r($json, true);
                        } else {
                            $json = array(
                                "detalle" => "No tiene los permisos suficientes",
                                "status" => "404"
                            );
                            return print_r($json, true);
                        }
                    }
                } else {
                    $json = array(
                        "detalle" => "Los registros no pueden estar vacios",
                        "status" => "404"
                    );
                }
            }
        }

        return json_encode($json, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $token = $request->header('Authorization');

        $clientes = Clientes::all();

        $json = array();


        foreach ($clientes as $key => $value) {

            if ('Basic ' . base64_encode($value['id_cliente'] . ':' . $value['llave_secreta']) == $token) {
            
                $validar = Cursos::where('id', $id)->get();

                if(!empty($validar)){

                    if($value['id'] == $validar[0]['id_cliente']){
                        $curso_eliminado = Cursos::where('id', $id)->delete();
                        $json = array(
                            "detalle" => "Se ha borrado el curso con exito",
                            "status" => "200"
                        );
                    }else{
                        $json = array(
                            "detalle" => "No tiene permisos para eliminar el curso",
                            "status" => "404"
                        );
                    }

                }else{
                    $json = array(
                        "detalle" => "El Curso no existe",
                        "status" => "404"
                    );
                }
            }
        }

        return json_encode($json, true);
    }
}
