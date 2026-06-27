<?php

// app/Models/DataPlotting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataPlotting extends Model
{
    protected $table = 'data_plotting';
    protected $fillable = ['berkas_id', 'foto_lokasi', 'geom'];

    public function berkas() {
        return $this->belongsTo(Berkas::class, 'berkas_id');
    }

    // Accessor untuk mengubah data Geometri dari DB ke GeoJSON di Frontend
    public function getGeojsonAttribute()
    {
        if (!$this->geom) return null;
        
        $result = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM data_plotting WHERE id = ?", [$this->id]);
        return json_decode($result[0]->geojson);
    }
}