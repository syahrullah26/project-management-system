<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reports;
use Illuminate\Validation\Rule;

class ReportsController extends Controller
{
    public function index()
    {
        $data = Reports::with([
            'project:id,name'
        ])->paginate(6);
        $data->getCollection()->transform(function ($report) {
            return [
                'id' => $report->id,
                'project' => $report->project ? [
                    'id' => $report->project->id,
                    'name' => $report->project->name,
                ] : null,
                'title' => $report->title,
                'description' => $report->description,
                'type' => $report->type,
                'status' => $report->status,
                'priority' => $report->priority,
            ];
        });

        return response()->json($data);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:project,id'], // table project
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['feature', 'bug'])],
            'status' => ['required', Rule::in(['onprogress', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        $report = Reports::create($validated);

        return response()->json([
            'success' => true,
            'data' => $report,
            'message' => 'Report berhasil dibuat.'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $report = Reports::findOrFail($id);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:project,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['feature', 'bug'])],
            'status' => ['required', Rule::in(['onprogress', 'done'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        $report->update($validated);

        return response()->json([
            'success' => true,
            'data' => $report->load(['project']),
            'message' => 'Report berhasil diperbarui.'
        ]);
    }

    public function updateStatus($id)
    {
        $report = Reports::findOrFail($id);

        if ($report->status !== 'onprogress') {
            return response()->json([
                'message' => 'Status sudah selesai'
            ], 400);
        }
        $report->update([
            'status' => 'done'
        ]);

        return response()->json([
            'message' => 'Status berhasil diubah ke done',
            'data' => $report
        ]);
    }



    public function destroy($id)
    {
        $report = Reports::findOrFail($id);
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report berhasil dihapus.'
        ]);
    }
}
