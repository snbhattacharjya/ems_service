<?php

namespace App\Http\Controllers;

use App\PoliceStation;
use Illuminate\Http\Request;

class PoliceStationController extends Controller
{
    public function getPoliceStations()
    {
        return PoliceStation::all();
    }
}
