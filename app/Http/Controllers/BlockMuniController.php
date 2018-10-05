<?php

namespace App\Http\Controllers;

use App\BlockMuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Response;
class BlockMuniController extends Controller
{
	 public function __construct()
    {	$this->userID=auth('api')->user()->user_id;
        $this->level=auth('api')->user()->level;
        $this->district=auth('api')->user()->area;
    }
    public function getBlockMunis()
    {
		return DB::select("select id,UPPER(name) as name,subdivision_id from block_munis where SUBSTRING(id,1,2)= ?",[$this->district]);
    }
}
