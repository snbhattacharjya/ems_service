<?php

namespace App\Http\Controllers;

use App\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'personnel_name' => 'required|string|max:50',
            'designation' => 'required|string|max:50',
            'office_id' => 'required',
        ]);

        $id = DB::select('SELECT MAX(CAST(SUBSTR(id,-5) AS UNSIGNED)) AS MaxID FROM personnel WHERE subdivision_id = ?',[$request->subdivision_id]);

        $id = $id[0]->MaxID;

        if(is_null($id)){
            $id = $request->subdivision_id.'0001';
        }
        else{
            $id = $request->subdivision_id.str_pad($id+1,4,"0",STR_PAD_LEFT);
        }


        $request = array_add($request,'id',$id);
        $request->validate([
            'id' => 'required|unique:personnels|digits:8'
        ]);

        $personnel =new personnel;
        $personnel->id = $request->id;
        $personnel->name = $request->personnel_name;
        $personnel->identification_code = $request->identification_code;
        $personnel->subdivision_id = $request->subdivision_id;

        $personnel->save();

        return response()->json($personnel,201);

    }
}
