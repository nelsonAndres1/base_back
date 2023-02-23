<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Horarios;
use Firebase\JWT\JWT;

class HorariosController extends Controller
{

    public $key;
    public function __construct()
    {
        $this->key = '_clave_-32118';
    }


    public function adicionar(Request $request)
    {

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $validate = Validator::make($params_array, [
            'horingam' => 'required',
            'horsalpm' => 'required',
            'detalle' => 'required',
        ]);

        if ($validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Datos Incorrectos',
                'errors' => $validate->errors()
            );
        } else {
            $horarios = new Horarios();
            $horarios->horingam = $params_array['horingam'];
            $horarios->horsalam = $params_array['horsalam'];
            $horarios->horingpm = $params_array['horingpm'];
            $horarios->horsalpm = $params_array['horsalpm'];
            $horarios->estado = $params_array['estado'];
            $horarios->id_tipo = $params_array['id_tipo'];
            $horarios->detalle = $params_array['detalle'];
            $horarios->save();

            if ($horarios) {
                $data = array(
                    'status' => 'success',
                    'code' => 400,
                    'message' => 'Datos Ingresados correctamente'
                );
            } else {
                $data = array(
                    'status' => 'success',
                    'code' => 400,
                    'message' => 'Datos No Ingresados'
                );
            }
        }

        return response()->json($data);
    }

    public function editar(Request $request)
    {

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $validate = Validator::make($params_array, [
            'id' => 'required',
            'horingam' => 'required',
            'horsalpm' => 'required',
            'detalle' => 'required',
        ]);

        if ($validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Datos Incorrectos',
                'errors' => $validate->errors()
            );
        } else {
            $horarios = Horarios::where('id', $params_array['id']);
            $horarios->horingam = $params_array['horingam'];
            $horarios->horsalam = $params_array['horsalam'];
            $horarios->horingpm = $params_array['horingpm'];
            $horarios->horsalpm = $params_array['horsalpm'];
            $horarios->estado = $params_array['estado'];
            $horarios->id_tipo = $params_array['id_tipo'];
            $horarios->detalle = $params_array['detalle'];
            $horarios->save();


            if ($horarios) {
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Datos Actualizados!'
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Datos No Actualizados!'
                );
            }
        }

        return response()->json($data);
    }

    public function eliminar(Request $request)
    {

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $validate = Validator::make($params_array, [
            'id' => 'required'
        ]);

        if ($validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Datos Incorrectos',
                'errors' => $validate->errors()
            );
        } else {

            $horarios = Horarios::where('id', $params_array['id'])->delete();

            if ($horarios) {
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Datos Eliminados!'
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Datos No Eliminados!'
                );
            }
        }
        return response()->json($data);
    }


    public function saveHorario(Request $request)
    {

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
            'id' => 'required',
            'horingam' => 'required',
            'horsalpm' => 'required',
            'estado' => 'required',
            'id_tipo' => 'required',
            'detalle' => 'required'
        ]);

        if ($validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        } else {
            $horarios = new Horarios();
            $horarios->horingam = $params_array['horingam'];
            $horarios->horsalam = $params_array['horsalam'];
            $horarios->horingpm=$params_array['horingpm'];
            $horarios->horsalpm=$params_array['horsalpm'];
            $horarios->estado=$params_array['estado'];
            $horarios->id_tipo=$params_array['id_tipo'];
            $horarios->detalle=$params_array['detalle'];
            $horarios->save();
            if($horarios){
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Datos Guardados!!'
                );
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Datos No Guardados!!'
                );
            }
        }
        return response()->json($data);
    }

    public function getConta28(Request $request){
        
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        }else{
            $data = $jwtAuth->getConta28();
        }
        $jwt = JWT::encode($data, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        return response()->json($decoded);
    }

    public function getHorarios(Request $request){
        
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        }else{
            $data = $jwtAuth->getHorarios();
        }

        return response()->json($data);
    }

    

}