<?php

namespace App\Http\Controllers;

use App\BlockMuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockMuniController extends Controller
{
    public function getBlockMunis()
    {
		return DB::select("select id,name,subdivision_id from block_munis where SUBSTRING(id,1,2)= ?",[$this->district]);
    }
}
