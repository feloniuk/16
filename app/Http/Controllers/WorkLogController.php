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
        WorkLog::create($request->validated());

        return redirect()->route('work-logs.index')
            ->with('success', 'Запис про роботу створено');
    }

    public function show(WorkLog $workLog)
    {
        $workLog->load(['branch', 'user', 'loggable']);

        return view('work-logs.show', compact('workLog'));
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
        $workLog->update($request->validated());

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
