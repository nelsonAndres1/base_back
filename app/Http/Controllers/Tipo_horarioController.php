<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Http\Request;
use App\Models\Horarios;
use Firebase\JWT\JWT;

class Tipo_horarioController extends Controller
{
    public $key;
    public function __construct()
    {
        $this->key = '_clave_-32118';
    }

    public function TipoHorario(Request $request){
        $jwtAuth  = new \JwtAuth();

        $data = $jwtAuth->TipoHorario();

        $jwt = JWT::encode($data, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        return response()->json($decoded);
    }

    

}
