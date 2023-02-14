<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Registro;
use Firebase\JWT\JWT;
use Nette\Utils\DateTime;


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
            if ($data['status'] != 'error') {
                $data = $this->Validacion_Register($data);
            }

            $jwt = JWT::encode($data, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        }
        return response()->json($decoded);
    }



    public function Validacion_Register($data)
    {

        $fecha = Date('Y-m-d');
        $hora = Date('H:i:s');
        $registro = '';
        $registro = Registro::where('docemp', $data['docemp'])->where('fecha', $fecha)->orderBy('id', 'desc')->first();
        if ($registro) {
            $fecini = new DateTime($fecha.' '.$hora);
            $fecfin = new DateTime($fecha.' '.$registro->hora);
            var_dump($fecini);
            var_dump($fecfin);
            $interval = $fecini->diff($fecfin);
            $diferencia = $interval->i;

            if($diferencia<=1){
                $data = array(
                    'status'=>'error',
                    'message'=>'Ya existe registro!',
                );
            }else{
                if ($registro->tipo == 'E') {
                    $respuesta = $this->register_tipo($data, 'S');
                    if($respuesta){
                        $data = array(
                            'status'=>'success',
                            'message'=>'Salida!',
                        );      
                    }  
                } else {
                    $respuesta = $this->register_tipo($data, 'E');

                    if($respuesta){
                        $data = array(
                            'status'=>'success',
                            'message'=>'Ingreso!',
                        );      
                    }  
                }
            }
        } else {
            $respuesta = $this->register_tipo($data, 'E');
            if($respuesta){
                $data = array(
                    'status'=>'success',
                    'message'=>'Ingreso!',
                );      
            }  
        }
        return $data;
    }


    public function register_tipo($data, $tipo)
    {

        $hora = date("H:i:s");
        $hoy = date("Y-m-d");

        $registro = new Registro();
        $registro->docemp = $data['docemp'];
        $registro->fecha = $hoy;
        $registro->hora = $hora;
        $registro->tipo = $tipo;
        $registro->usrsede = '1';
        $registro->id_horario = 1;
        $registro->save();


        if($registro){
            return true;
        }else{
            return false;
        }
    }



}