<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Http\Request;
use App\Models\Horarios;

class HorariosController extends Controller
{

    public function adicionar(Request $request){


        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $validate = Validator::make($params_array, [
            'horingam'=>'required',
            'horsalpm'=>'required',
            'detalle'=>'required',
        ]);

        if($validate->fails()){
            $data = array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Datos Incorrectos',
                'errors'=>$validate->errors()
            );
        }else{
            $horarios = new Horarios();
            $horarios->horingam= $params_array['horingam'];
            $horarios->horsalam= $params_array['horsalam'];
            $horarios->horingpm= $params_array['horingpm'];
            $horarios->horsalpm= $params_array['horsalpm'];
            $horarios->detalle= $params_array['detalle'];
            $horarios->save();

            if($horarios){
                $data = array(
                    'status'=>'success',
                    'code'=>400,
                    'message'=>'Datos Ingresados correctamente'
                );
            }else{
                $data = array(
                    'status'=>'success',
                    'code'=>400,
                    'message'=>'Datos No Ingresados'
                );
            }
        }

        return response()->json($data);
    }

    public function editar(Request $request){


        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $validate = Validator::make($params_array, [
            'id'=>'required',
            'horingam'=>'required',
            'horsalpm'=>'required',
            'detalle'=>'required',
        ]);

        if($validate->fails()){
            $data = array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Datos Incorrectos',
                'errors'=>$validate->errors()
            );
        }else{

/*             $registro = Registro::where('id',$params_array['id']);

            if($horarios){
                $data = array(
                    'status'=>'success',
                    'code'=>400,
                    'message'=>'Datos Ingresados correctamente'
                );
            }else{
                $data = array(
                    'status'=>'success',
                    'code'=>400,
                    'message'=>'Datos No Ingresados'
                );
            } */
        }

        return response()->json($data);
    }

}
