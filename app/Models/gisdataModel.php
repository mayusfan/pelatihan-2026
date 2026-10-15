<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class gisdataModel extends Model
{
    protected $table = 'Contoh_Permohonan';
    // protected $guarded = ['id'];

    public function geojson_data()
    {
        return $this
            ->selectRaw('st_asgeojson(geom) as geom, "pemohon", "tgl_surat", "keterangan", "wadmpr"')
            ->get();
    }
}
