<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;;

class ApiController extends Controller
{
    public function getData()
    {

        return response()->json(['message' => 'Data berhasil diambil'], 200);
    }

    public function storeData(Request $request)
    {

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function deleteById()
    {

        return response()->json(['message' => 'Data berhasil Dihapus'], 200);
    }
}
