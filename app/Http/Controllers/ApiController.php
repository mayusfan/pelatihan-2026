<?php

namespace App\Http\Controllers;

use App\Models\gisdataModel;
use Illuminate\Http\Request;


class ApiController extends Controller
{
    public function __construct()
    {
        $this->gisdata = new gisdataModel();
    }

    public function geojson_data()
    {
        $points = $this->gisdata->geojson_data();

        $feature = array();

        foreach ($points as $data) {
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode($data->geom),
                'properties' => [
                    'Pemohon' => $data->pemohon,
                    'Tgl_Surat' => $data->tgl_surat,
                    'Keterangan' => $data->keterangan,
                    'WADMPR' => $data->wadmpr,
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
