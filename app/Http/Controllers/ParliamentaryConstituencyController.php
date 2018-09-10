<?php

namespace App\Http\Controllers;

use App\ParliamentaryConstituency;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class ParliamentaryConstituencyController extends Controller
{
    public function getPcs()
    {
        return ParliamentaryConstituency::all();
    }
}
