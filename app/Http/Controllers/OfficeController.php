<?php

namespace App\Http\Controllers;

use App\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeController extends Controller
{
    public function getAllOffices()
    {
        return Office::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'office_name' => 'required|string|max:50',
            'identification_code' => 'required|string|max:50',
            'subdivision_id' => 'required',
        ]);

        $id = DB::select('SELECT MAX(CAST(SUBSTR(id,-4) AS UNSIGNED)) AS MaxID FROM offices WHERE subdivision_id = ?',[$request->subdivision_id]);

        $id = $id[0]->MaxID;

        if(is_null($id)){
            $id = $request->subdivision_id.'0001';
        }
        else{
            $id = $request->subdivision_id.str_pad($id+1,4,"0",STR_PAD_LEFT);
        }


        $request = array_add($request,'id',$id);
        $request->validate([
            'id' => 'required|unique:offices|digits:8'
        ]);

        $office =new Office;
        $office->id = $request->id;
        $office->name = $request->office_name;
        $office->identification_code = $request->identification_code;
        $office->officer_designation =  $request->officer_designation;
        $office->address =  $request->office_address;
        $office->post_office =  $request->post_office;
        $office->pin =  $request->pin;
        $office->subdivision_id = $request->subdivision_id;
        $office->district_id = '13';
        $office->block_muni_id =  $request->block_muni_id;
        $office->police_station_id =  $request->police_station_id;
        $office->ac_id =  $request->ac_id;
        $office->pc_id =  $request->pc_id;
        $office->category_id =  $request->category_id;
        $office->institute_id =  $request->institute_id;
        $office->identification_code =  $request->identification_code;
        $office->email =  $request->email;
        $office->phone =  $request->phone;
        $office->mobile =  $request->mobile;
        $office->fax =  $request->fax;
        $office->total_staff =  $request->total_staff;
        $office->male_staff =  $request->male_staff;
        $office->female_staff =  $request->female_staff;

        $office->save();

        return response()->json($office,201);

    }
}
