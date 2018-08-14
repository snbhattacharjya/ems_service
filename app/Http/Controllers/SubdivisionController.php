<?php

namespace App\Http\Controllers;

use App\Subdivision;
use Illuminate\Http\Request;

class SubdivisionController extends Controller
{
    public function getSubdivisions(){
        return Subdivision::all();
    }
}
