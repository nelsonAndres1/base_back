<?php

namespace App\Helpers;

use App\Models\Horarios;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\Gener02;
use App\Models\Gener28;
use App\Models\Nomin02;
use App\Models\Conta28;
use App\Models\Registro;
use App\Models\tipo_horario;
use App\Models\trabajador_horario;

/* require_once("/resources/libs/UserReportPdf/UserReportPdf.php");
require_once("/resources/libs/UserReportExcel/UserReportExcel.php");
*/
class JwtAuth
{

    public $key;


    public function __construct()
    {
        $this->key = '_clave_-32118';
    }

    function eliminar_acentos($cadena)
    {

        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena
        );

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena
        );

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena
        );

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena
        );

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        $cadena = str_replace(
            array('¤', '¥'),
            array('N', 'N'),
            $cadena
        );

        return $cadena;
    }



    function write_to_console($data)
    {
        $console = $data;
        if (is_array($console)) {
            $console = implode(',', $console);
        }
        echo "<script>console.log('Console: " . $console . "' );</script>";
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

    public function signup($usuario, $clave, $getToken = null)
    {
        //Buscar
        $gener02 = Gener02::where([
            'usuario' => $usuario,
            'clave' => $clave
        ])->first();
        //Comprobar si son correctas
        $signup = false;
        if (is_object($gener02)) {
            $signup = true;
        }
        //Generar el token con los datos del identificado
        if ($signup) {
            $token = array(
                'sub' => $gener02->usuario,
                'email' => $gener02->email,
                'name' => $gener02->nombre,
                'cedtra' => $gener02->cedtra,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
                'sede' => $gener02->sede
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            //Devolver los datos identificados o el token, en funcion de un parametro
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            if (is_null($getToken)) {
                $data = $jwt;

            } else {
                $data = $decoded;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login Incorrecto'
            );
        }
        return $data;
    }


    public function validateNomin02($docemp)
    {

        $nomin02 = Nomin02::where('docemp', $docemp)->first();
        if ($nomin02) {
            $conta28 = Conta28::where('coddep', $nomin02->coddep)->where('cnt', '01')->first();
            if ($nomin02->estado == 'A') {
                $data = array(
                    'docemp' => $nomin02->docemp,
                    'priape' => $nomin02->priape,
                    'segape' => $nomin02->segape,
                    'nomemp' => $nomin02->nomemp,
                    'segnom' => $nomin02->segnom,
                    'coddep' => $nomin02->coddep,
                    'detalle_coddep' => $conta28->detalle,
                    'code' => 200,
                    'status' => 'success'
                );

            } else {
                $data = array(
                    'status' => 'error',
                    'message' => 'Trabajador Inactivo!',
                    'bandera' => false,
                    'code' => 404
                );
            }

        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Documento no existe',
                'bandera' => false,
                'code' => 404
            );
        }

        return $data;

    }

    public function TipoHorario()
    {

        $tipo_horario = tipo_horario::all();
        $arrayTH = array();
        if ($tipo_horario) {
            foreach ($tipo_horario as $th) {
                $array = array(
                    'id' => $th->id,
                    'detalle' => trim($th->detalle)
                );
                array_push($arrayTH, $array);
            }
        } else {
            $arrayTH = array(
                'status' => 'error',
                'message' => 'No existen datos'
            );
        }
        return $arrayTH;

    }

    public function getConta28()
    {

        $conta28 = Conta28::where('estado', 'A')->where('cnt', '01')->get();
        $arrayC = array();
        if ($conta28) {
            foreach ($conta28 as $key) {
                $array = array(
                    'coddep' => $key->coddep,
                    'detalle' => $key->detalle,
                );
                array_push($arrayC, $array);
            }
        } else {
            $arrayC = array(
                'status' => 'error',
                'message' => 'No existen datos'
            );
        }
        return $arrayC;
    }


    public function getNomin02($coddep)
    {

        $nomin02 = Nomin02::where('coddep', $coddep)->where('estado', 'A')->get();
        $arrayN = array();
        if ($nomin02) {
            foreach ($nomin02 as $key) {
                $array = array(
                    'nomemp' => trim(utf8_decode($key->nomemp)) . ' ' . trim(utf8_decode($key->segnom)) . ' ' . trim(utf8_decode($key->priape)) . ' ' . trim(utf8_decode($key->segape)),
                    'docemp' => $key->docemp,
                );
                array_push($arrayN, $array);
            }
        } else {
            $arrayN = array(
                'status' => 'error',
                'message' => 'No existen datos'
            );
        }
        return $arrayN;
    }




    public function getHorarios()
    {
        $horarios = Horarios::where('estado', 'A')->get();
        $arrayH = array();
        if ($horarios) {
            foreach ($horarios as $ho) {
                $tipo_horario = tipo_horario::where('id', $ho->id_tipo)->first();
                $array = array(
                    'id' => $ho->id,
                    'horingam' => $ho->horingam,
                    'horsalam' => $ho->horsalam,
                    'horingpm' => $ho->horingpm,
                    'horsalpm' => $ho->horsalpm,
                    'estado' => $ho->estado,
                    'tipo_detalle' => utf8_decode(trim($tipo_horario->detalle)),
                    'detalle' => $ho->detalle
                );
                array_push($arrayH, $array);
            }
        } else {
            $arrayH = array(
                'status' => 'error',
                'message' => 'No existen datos'
            );
        }
        $jwt = JWT::encode($arrayH, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        return $decoded;
    }
    public function traerUltimo($usuario)
    {
        $date = Date('Y-m-d');
        $registro = Registro::where('usrsede', $usuario)->where('fecha', $date)->orderBy('id', 'DESC')->first();

        if ($registro) {

            $nomin02 = Nomin02::where('docemp', $registro->docemp)->first();
            $array = array(
                'nombre' => trim(utf8_decode($nomin02->nomemp)) . ' ' . trim(utf8_decode($nomin02->segnom)) . ' ' . trim(utf8_decode($nomin02->priape)) . ' ' . trim(utf8_decode($nomin02->segape)),
                'docemp' => $registro->docemp,
            );
        } else {
            $array = array(
                'status' => 'error',
                'message' => 'No existen datos'
            );
        }
        $jwt = JWT::encode($array, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        return $decoded;
    }
    public function permisos($usuario)
    {
        $gener02 = Gener02::where('usuario', $usuario)->first();
        $arrayP = array();
        if ($gener02) {
            $gener28 = Gener28::where('role', $gener02->tipfun)->where('codapl', 'ET')->get();
            if ($gener28) {
                foreach ($gener28 as $key) {
                    $array = array(
                        'resource' => $key->resource,
                        'action' => $key->action,
                        'allow' => $key->allow
                    );
                array_push($arrayP, $array);
                }
            }else{
                $arrayP = array(
                    'status' => 'error',
                    'message' => 'No existen datos'
                );
            }
        } else {
            $arrayP = array(
                'status' => 'error',
                'message' => 'No existen datos'
            );
        }

        $jwt = JWT::encode($arrayP, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        return $decoded;

    }

}