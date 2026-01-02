<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class projectController extends Controller
{
    public function index() {
        $projects = Project::latest()->paginate(6);

        $projects-> getCollection()->transform(function($project){
            return [
                'id' => $project->id,
                'name'=> $project-> name
            ];
        });
    }

    public function store(Request $request, $id){
        $validated = $request-> validate([
            'name' => 'required|string|max:255'
        ]);
        $project = Project::create($validated);

        return response()-> json([
            'success' => true,
            'data'=> $project,
            'message' => 'project berhasil dibuat',
        ], 201);
    }
    

    public function update(Request $request, $id){
        $project = Project::findOrFail($id);

        $validated= $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $project->update($validated);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project berahasil diupdate'
        ]);

    }
    public function destroy($id){
        $project = Project::findOrFail($id);
        $project-> delete();

        return response()->json([
            'success' =>true,
            'message'=> 'Project Berhasil dihapus'

        ]);

    }
}
