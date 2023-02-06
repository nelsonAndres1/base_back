<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Gener02;

class Gener02Controller extends Controller
{
    public function pruebas(Request $request){
        return "Accion User!";
    }
    public function register(Request $request){
        
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);        
        //Limpiar los datos
        if(!empty($params) && !empty($params_array)){
            $params_array = array_map('trim', $params_array);//Limpiar los datos 
            
            //validar datos
            $validate = Validator::make($params_array,[
                'usuario' =>'required',
                'cedtra' =>'required',
                'nombre' => 'required',
                'email' => 'required|email|unique:gener02s',//validar que el usuario sea unico
                'clave' => 'required',
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code'   => 404,
                    'message' => 'No creado',
                    'errors' => $validate->errors()
                );
            }else{
                //cifrar contraseña
                $pwd = hash('sha256', $params->clave);
                //crear el usuario
                $gener02 = new Gener02();
                $gener02->usuario=$params_array['usuario'];
                $gener02->cedtra=$params_array['cedtra'];
                $gener02->nombre=$params_array['nombre'];
                $gener02->email=$params_array['email'];
                $gener02->clave=$pwd;
                $gener02->save();

                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message' => 'si creado',
                    'user' => $gener02
                );
            }
        }else{                  
            $data = array(
                'status' => 'error',
                'code'   => 404,
                'message' => 'Datos enviados no correctos'      
            );
        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        $jwtAuth = new \JwtAuth();
        //Recibir datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        //validar los datos

        $validate = Validator::make($params_array,[
            'usuario' =>'required',
            'clave' => 'required'
        ]);
        
        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code'   => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        }else{            

            $pwd = $params->clave;

            $clave = $pwd;
            $signup = $jwtAuth->signup($params->usuario, $clave);
            if(!empty($params->gettoken)){
                $signup = $jwtAuth->signup($params->usuario, $clave, true);
            }
        }
        return response()->json($signup,200);
    }
    public function encriptar($clave){
        $mclave = '';
        for($i=0;$i<strlen($clave);$i++){
            if($i%2!=0){
                $x=6;
            }else{
                $x=-4;
            }
            $mclave .= chr(ord(substr($clave,$i,1)) + $x + 5);
        }
        return $mclave;
    }
    public function update (Request $request){
        //Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        
        if($checkToken){
            $data= array(
                'code'=>200,
                'status' =>'success',
                'message' => 'usuario identificado'
            );
        }else{
            $data= array(
                'code'=>400,
                'status' =>'error',
                'message' => 'usuario no identificado'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function findGener02(Request $request){
        $jwtAuth = new \JwtAuth();

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array,[
            'cedtra'=>'required'
        ]);
        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code'   => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        }else{
            $signup = $jwtAuth->findGener02($params->cedtra);
            if(!empty($params->gettoken)){
                $signup = $jwtAuth->findGener02($params->cedtra);
            }
        }
        return response()->json($signup, 200);
    }
    public function permisos(Request $request){
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array,[
            'docemp'=>'required'
        ]);
        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code'   => 404,
                'errors' => $validate->errors()
            );
        }else{
            $signup = $jwtAuth->permisos($params->docemp);
            if(!empty($params->gettoken)){
                $signup = $jwtAuth->permisos($params->docemp);
            }
        }
        return response()->json($signup, 200);
    }
}
