<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Registro;
use Firebase\JWT\JWT;

class RegistroController extends Controller
{
    public $key;
    public function __construct()
    {
        $this->key = '_clave_-32118';
    }
    public function validateNomin02(Request $request)
    {
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
            'docemp' => 'required'
        ]);


        if ($validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        } else {
            $data = $jwtAuth->validateNomin02($params_array['docemp']);
            if($data['status'] != 'error'){
                $this->Validacion_Register($data);
            }

            $jwt = JWT::encode($data, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        }
        return response()->json($decoded);
    }



    public function Validacion_Register($data)
    {   
        $jwtAuth = new \JwtAuth();

        $fecha = Date('Y-m-d'); 
        
        $registro = $jwtAuth->validacionTipo($data['docemp']);
    
    
        $registro =  Registro::where('docemp', $data['docemp'])->where('fecha', "'".$fecha."'")->orderBy('id', 'desc')->first();

        if ($registro) {

            if ($registro->tipo == 'E') {

                //iria registro para salida
                $this->register_tipo($data, 'S');
            } else {

                //iria registro para Entrada
                $this->register_tipo($data, 'E');
            }
        } else {
               //iria registro para Entrada
               $this->register_tipo($data, 'E');
        }
    }


    public function register_tipo($data, $tipo){

        $hora = date("H:i:s");
        $hoy = date("Y-m-d");  

        $registro = new Registro();
        $registro->docemp=$data['docemp'];
        $registro->fecha=$hoy;
        $registro->hora = $hora;
        $registro->tipo = $tipo;
        $registro->usrsede = '1';
        $registro->id_horario = 1;
        $registro->save();
    }



}