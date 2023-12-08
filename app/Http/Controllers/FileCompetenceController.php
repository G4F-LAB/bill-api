<?php

namespace App\Http\Controllers;

use App\Models\FileCompetence;
use Illuminate\Http\Request;

class FileCompetenceController extends Controller
{
    public function getAll(){
        $fileCompetence = FileCompetence::all();
        return response()->json($fileCompetence,200);
    }
}
