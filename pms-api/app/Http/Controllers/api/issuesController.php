<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class issuesController extends Controller
{
    public function index()
    {
        $issues = Issue::with([
            'project:id,name'
        ])->paginate(6);

        $issues->getCollection()->transform(function ($issue) {
            return [
                'id' => $issue->id,
                'project' => [
                    'id' => $issue->project->id,
                    'name' => $issue->project->name,
                ],
                'title' => $issue->title,
                'description' => $issue->description,
                'type' => $issue->type,
                'status' => $issue->status,
                'priority' => $issue->priority
            ];
        });
        return response()->json($issues);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exist:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['feature', 'bug'])],
            'status' => ['required', Rule::in(['onprogress', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])]
        ]);
        $issue = Issue::create($validated);

        return response()->json([
            'success' => true,
            'data' => $issue,
            'message' => 'report berhasil dibuat'
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $issue = Issue::findOrFail($id);
        $validated = $request->validate([
            'project_id' => ['required', 'exist:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['feature', 'bug'])],
            'status' => ['required', Rule::in(['onprogress', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])]

        ]);

        $issue->update($validated);
        return response()->json([
            'success' => true,
            'data' => $issue->load(['project']),
            'message' => 'report berhasil diperbarui'
        ]);

    }
    public function destroy($id) {
        $issue= Issue::findOrFail($id);
        $issue->delete();

        return response()->json([
            'success'=>true,
            'message'=> 'Report berhasil dihapus'
        ]);

    }
}
