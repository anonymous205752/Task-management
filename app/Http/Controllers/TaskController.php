<?php 
    namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Notifications\TaskCreatedNotification;

class TaskController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user();
    $query = $user->tasks()->latest();

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    if ($request->has('priority')) {
        $query->where('priority', $request->priority);
    }

    if ($request->has('search')) {
        $search = $request->search;
        $query->where('title', 'like', "%$search%");
    }

    return response()->json($query->paginate(10));
}



    public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'in:pending,in_progress,completed',
        'priority' => 'in:low,medium,high',
        'due_date' => 'nullable|date',
    ]);

    $task = $request->user()->tasks()->create($data);

    // Send notification (queued)
    $request->user()->notify(new TaskCreatedNotification($task));

    return response()->json($task, 201);
}
    public function show(Task $task)
    {
        $this->authorizeTask($task);
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update($data);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    private function authorizeTask(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }
    public function markComplete($id, Request $request)
{
    $task = $request->user()->tasks()->findOrFail($id);
    $task->status = 'completed';
    $task->save();

    return response()->json(['message' => 'Task marked as completed']);
}
public function overdue(Request $request)
{
    $tasks = $request->user()->tasks()
        ->whereDate('due_date', '<', now())
        ->where('status', '!=', 'completed')
        ->latest()
        ->get();

    return response()->json($tasks);
}
public function restore($id, Request $request)
{
    $task = $request->user()->tasks()->withTrashed()->findOrFail($id);
    $task->restore();

    return response()->json(['message' => 'Task restored successfully']);
}



}
