<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Registro;
use Firebase\JWT\JWT;
use App\Models\trabajador_horario;
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
            $data = $jwtAuth->validateNomin02((int)$params_array['docemp']);
            if ($data['status'] != 'error') {
                $data = $this->Validacion_Register($data, $params_array['usuario']);
            }


        }
        
        $jwt = JWT::encode($data, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        return response()->json($decoded);
    }



    public function Validacion_Register($data, $usuario)
    {
        $fecha = Date('Y-m-d');
        $hora = Date('H:i:s');        
        $registro = '';
        $registro = Registro::where('docemp', $data['docemp'])->where('fecha', $fecha)->orderBy('id', 'desc')->first();
        if ($registro) {
            $fecini = new DateTime($fecha . ' ' . $hora);
            $fecfin = new DateTime($fecha . ' ' . $registro->hora);
            $interval = $fecini->diff($fecfin);
            $diferencia = $interval->i;
            if ((int)$diferencia < 1) {
                $data = array(
                    'status' => 'error',
                    'nombre' => $this->convert_from_latin1_to_utf8_recursively(trim($data['nomemp']).' '.trim($data['segnom']).' '.trim($data['priape']).' '.trim($data['segape'])),
                    'hora'=> $hora,
                    'message' => 'Ya existe registro!',
                );
            } else {
                if ($registro->tipo == 'E') {
                    $respuesta = $this->register_tipo($data, 'S',$usuario);
                    if ($respuesta) {
                        $data = array(
                            'status' => 'success',
                            'nombre' => $this->convert_from_latin1_to_utf8_recursively(trim($data['nomemp']).' '.trim($data['segnom']).' '.trim($data['priape']).' '.trim($data['segape'])),
                            'hora'=> $hora,
                            'message' => 'Salida!',
                        );
                    }
                } else {
                    $respuesta = $this->register_tipo($data, 'E',$usuario);

                    if ($respuesta) {
                        $data = array(
                            'status' => 'success',
                            'nombre' => $this->convert_from_latin1_to_utf8_recursively(trim($data['nomemp']).' '.trim($data['segnom']).' '.trim($data['priape']).' '.trim($data['segape'])),
                            'hora'=> $hora,
                            'message' => 'Ingreso!',
                        );
                    }
                }
            }
        } else {
            $respuesta = $this->register_tipo($data, 'E', $usuario);
            if ($respuesta) {
                $data = array(
                    'status' => 'success',
                    'nombre' => $this->convert_from_latin1_to_utf8_recursively(trim($data['nomemp']).' '.trim($data['segnom']).' '.trim($data['priape']).' '.trim($data['segape'])),
                    'hora'=> $hora,
                    'message' => 'Ingreso!',
                );
            }
        }

        $jwt = JWT::encode($data, $this->key, 'HS256');
        //Devolver los datos identificados o el token, en funcion de un parametro
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        return $decoded;
    }




    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d)
                $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d)
                $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }

    public function register_tipo($data, $tipo,$usuario)
    {


        try {
            $hora = date("H:i:s");
            $hoy = date("Y-m-d");

            $trabajador_horario = trabajador_horario::where('docemp', $data['docemp'])->where("estado", "A")->first(); 

            if($trabajador_horario){
                $registro = new Registro();
                $registro->docemp = $data['docemp'];
                $registro->fecha = $hoy;
                $registro->hora = $hora;
                $registro->tipo = $tipo;
                $registro->usrsede = $usuario;
                $registro->id_horario = $trabajador_horario->id_horario;
                $registro->save();
        
                if ($registro) {
                    return true;
                } else {
                    return false;
                }
            }else{
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }

    }



}