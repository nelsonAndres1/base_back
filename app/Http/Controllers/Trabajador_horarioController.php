<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conta28;

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
}