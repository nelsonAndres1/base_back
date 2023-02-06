<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function permits(Request $request){

        $jwtAuth = new \JwtAuth();

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params) && !empty($params_array)){
            $params_array = array_map('trim', $params_array);

            $validate = Validator::make($params_array, [
                'usuario'=>'required'
            ]);
            if($validate->fails()){
                $data = array(
                    'status'=>'error',
                    'code'=>404,
                    'message'=>'No!',
                    'errors'=>$validate->errors()
                );
            }else{

                $data = $jwtAuth->traerPermisos($params->usuario);
                if(!empty($params->gettoken)){
                    $data = $jwtAuth->traerPermisos($params->usuario);
                }
            }
            return response()->json($data,200);
        }
    }

}
