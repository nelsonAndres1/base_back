<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Horarios;
use Firebase\JWT\JWT;

class Nomin02Controller extends Controller
{

    public $key;
    public function __construct()
    {
        $this->key = '_clave_-32118';
    }

    public function getNomin02(Request $request){

        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
            'coddep'=>'required'
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        }else{
            $data = $jwtAuth->getNomin02($params_array['coddep']);
        }
        $jwt = JWT::encode($data, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        return response()->json($decoded);
    }


    public function traerUltimo(Request $request){

        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
            'usuario'=>'required'
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Error!',
                'errors' => $validate->errors()
            );
        }else{
            $data = $jwtAuth->traerUltimo($params_array['usuario']);
        }
        return response()->json($data);
    }

}
