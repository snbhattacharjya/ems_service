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
        $office->subdivision_id = $request->subdivision_id;

        $office->save();

        return response()->json($office,201);

    }
}
