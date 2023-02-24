<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Conta28;
use Illuminate\Support\Facades\Validator;
use App\Models\trabajador_horario;

class Trabajador_horarioController extends Controller
{
    public function searchConta28(Request $request)
    {
        $res = '';
        $query = Conta28::query();
        $data = $request->input('search');
        if ($data != '') {
            $query->whereRaw("coddep LIKE '%" . $data . "%'")
                ->orWhereRaw("detalle LIKE '%" . $data . "%'");

            $res = $this->convert_from_latin1_to_utf8_recursively($query->get());
            if ($res) {
                for ($i = 0; $i < count($res); $i++) {
                    $res[$i]['detalle'] = utf8_decode($res[$i]['detalle']);
                }
            }
        } else {
            $query = '';
            $res = $this->convert_from_latin1_to_utf8_recursively($query);
        }
        return $res;
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

    public function guardarTrabajadorHorario(Request $request)
    {
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array, [
            'id_horario' => 'required',
        ]);
        if ($validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No Logeado',
                'errors' => $validate->errors()
            );
        } else {
            try {
                foreach ($params_array['docemp'] as $key) {
                    $trabajador_horario = trabajador_horario::where('docemp', $key)->where("estado", "A")->where('id_horario', $params_array['id_horario'])->first();
                    if(!$trabajador_horario){
                        $trabajador_horario = trabajador_horario::where('docemp', $key)->where("estado", "A")->update(['estado' => 'I']);
                        $trabajador_horario = new trabajador_horario();
                        $trabajador_horario->docemp = $key;
                        $trabajador_horario->id_horario = $params_array['id_horario'];
                        $trabajador_horario->estado = "A";
                        $trabajador_horario->save();
                    }
                }

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Datos Insertados',
                );


            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error al insertar datos!',
                    'errors' => $e
                );

            }

            return $data;
        }
    }

}