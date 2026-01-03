<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(6);

        if ($projects->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data project',
                'data' => [],
                'meta' => [
                    'current_page' => $projects->currentPage(),
                    'total' => 0,
                ]
            ], 200);
        }

        $projects->getCollection()->transform(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
            ];
        });

        return response()->json($projects, 200);
    }


    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $project = Project::create($validated);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'project berhasil dibuat',
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project berahasil diupdate'
        ]);
    }
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project Berhasil dihapus'

        ]);
    }
}
