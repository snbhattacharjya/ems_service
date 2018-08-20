<?php

namespace App\Http\Controllers;

use App\ParliamentaryConstituency;
use Illuminate\Http\Request;

class ParliamentaryConstituencyController extends Controller
{
    public function getPcs()
    {
        return ParliamentaryConstituency::all();
    }
}
