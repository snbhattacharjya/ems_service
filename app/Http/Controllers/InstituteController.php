<?php

namespace App\Http\Controllers;

use App\Institute;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class InstituteController extends Controller
{
    public function getInstitutes()
    {
        return Institute::all();
    }
}
