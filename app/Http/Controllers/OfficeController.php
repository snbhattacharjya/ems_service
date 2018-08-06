<?php

namespace App\Http\Controllers;

use App\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function getAllOffices()
    {
        return Office::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'identification_code' => 'required|string|max:50',
            'subdivision_id' => 'required',
        ]);

        $office =new Office;
        $office->id = $request->id;
        $office->name = $request->name;
        $office->identification_code = $request->identification_code;
        $office->subdivision_id = $request->subdivision_id;

        $office->save();

        return response()->json($office);

    }
}
