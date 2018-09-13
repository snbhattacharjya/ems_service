<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\ParliamentaryConstituency;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class ParliamentaryConstituencyController extends Controller
{
    public function getPcs()
    {

        $area=auth('api')->user()->area;
        return $parl = DB::table('parliamentary_constituencies')
            ->groupBy('parliamentary_constituencies.id','parliamentary_constituencies.name')
           ->select('parliamentary_constituencies.id','parliamentary_constituencies.name')
           ->join('assembly_constituencies', 'parliamentary_constituencies.id', '=', 'assembly_constituencies.pc_id')
            ->where('assembly_constituencies.district_id', $area)
        ->get();
    // return ParliamentaryConstituency::all();
    }
}
