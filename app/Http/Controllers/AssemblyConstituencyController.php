<?php

namespace App\Http\Controllers;

use App\AssemblyConstituency;
use Illuminate\Http\Request;

class AssemblyConstituencyController extends Controller
{
    public function getAssemblies()
    {
        return AssemblyConstituency::all();
    }
}
