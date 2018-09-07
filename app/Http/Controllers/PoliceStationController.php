<?php

namespace App\Http\Controllers;

use App\PoliceStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PoliceStationController extends Controller
{
    public function getPoliceStations()
    {
       return DB::select("select id,name,subdivision_id from police_stations where SUBSTRING(id,1,2)= ?",[$this->district]);
    }
}
