<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\Gener02;


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
                'sede'=>$gener02->sede
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

   
}