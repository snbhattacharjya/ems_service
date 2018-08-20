<?php

namespace App\Http\Controllers;

use App\BlockMuni;
use Illuminate\Http\Request;

class BlockMuniController extends Controller
{
    public function getBlockMunis()
    {
        return BlockMuni::all();
    }
}
