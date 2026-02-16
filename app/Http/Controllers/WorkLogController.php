<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkLogRequest;
use App\Http\Requests\UpdateWorkLogRequest;
use App\Models\Branch;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class WorkLogController extends Controller
{
    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'У вас нет доступа к цій операції');
        }
    }

    public function index(Request $request)
    {
        $query = WorkLog::with(['branch', 'user']);

        if ($request->filled('work_type')) {
            $query->byWorkType($request->work_type);
        }

        if ($request->filled('branch_id')) {
            $query->byBranch($request->branch_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $workLogs = $query->orderBy('performed_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $workLogs->appends($request->query());

        $branches = Branch::where('is_active', true)->get();

        return view('work-logs.index', compact('workLogs', 'branches'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $branches = Branch::where('is_active', true)->get();

        return view('work-logs.create', compact('branches'));
    }

    public function store(StoreWorkLogRequest $request)
    {
        $this->authorizeAdmin();
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        WorkLog::create($data);

        return redirect()->route('work-logs.index')
            ->with('success', 'Запис про роботу створено');
    }

    public function show(WorkLog $workLog)
    {
        $workLog->load(['branch', 'user', 'loggable']);

        if (request()->expectsJson()) {
            return response()->json($this->formatWorkLogData($workLog));
        }

        return view('work-logs.show', compact('workLog'));
    }

    private function formatWorkLogData(WorkLog $workLog): array
    {
        return [
            'id' => $workLog->id,
            'work_type' => $workLog->work_type,
            'work_type_label' => $workLog->getWorkTypeLabel(),
            'description' => $workLog->description,
            'branch_name' => $workLog->branch?->name ?? '-',
            'room_number' => $workLog->room_number ?? '-',
            'performed_at' => $workLog->performed_at->format('d.m.Y'),
            'user_name' => $workLog->user?->name ?? '-',
            'notes' => $workLog->notes,
            'has_loggable' => (bool) $workLog->loggable,
            'loggable_type' => $workLog->loggable ? class_basename($workLog->loggable_type) : null,
            'loggable_id' => $workLog->loggable_id,
            'created_at' => $workLog->created_at->format('d.m.Y H:i:s'),
            'updated_at' => $workLog->updated_at->format('d.m.Y H:i:s'),
            'updated_differs' => $workLog->updated_at != $workLog->created_at,
            'is_admin' => auth()->user()->role === 'admin',
            'edit_url' => route('work-logs.edit', $workLog),
            'delete_url' => route('work-logs.destroy', $workLog),
        ];
    }

    public function edit(WorkLog $workLog)
    {
        $this->authorizeAdmin();
        $branches = Branch::where('is_active', true)->get();

        return view('work-logs.edit', compact('workLog', 'branches'));
    }

    public function update(UpdateWorkLogRequest $request, WorkLog $workLog)
    {
        $this->authorizeAdmin();
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $workLog->update($data);

        return redirect()->route('work-logs.show', $workLog)
            ->with('success', 'Запис про роботу оновлено');
    }

    public function destroy(WorkLog $workLog)
    {
        $this->authorizeAdmin();
        $workLog->delete();

        return redirect()->route('work-logs.index')
            ->with('success', 'Запис про роботу видалено');
    }
}
