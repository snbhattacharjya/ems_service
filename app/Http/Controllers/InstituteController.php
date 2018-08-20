<?php

namespace App\Http\Controllers;

use App\Institute;
use Illuminate\Http\Request;

class InstituteController extends Controller
{
    public function getInstitutes()
    {
        return Institute::all();
    }
}
