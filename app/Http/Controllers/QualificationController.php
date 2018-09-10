<?php

namespace App\Http\Controllers;

use App\Qualification;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class QualificationController extends Controller
{
    public function getQualifications()
    {
        return Qualification::all();
    }
}
