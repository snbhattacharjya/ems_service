<?php

namespace App\Http\Controllers;

use App\Qualification;
use Illuminate\Http\Request;

class QualificationController extends Controller
{
    public function getQualifications()
    {
        return Qualification::all();
    }
}
