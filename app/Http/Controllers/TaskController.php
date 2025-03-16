<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
  use AuthorizesRequests;

  /**
   * Display a listing of the resource.
   */
  public function index(): Response
  {
    return Inertia::render('Tasks/Index', [
      'tasks' => auth()->user()->tasks()
        ->orderBy('created_at', 'desc')
        ->get(),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'due_date' => 'nullable|date',
    ]);

    $request->user()->tasks()->create($validated);

    return redirect(route('tasks.index'));
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Task $task)
  {
    $this->authorize('update', $task);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'completed' => 'boolean',
      'due_date' => 'nullable|date',
    ]);

    $task->update($validated);

    return redirect(route('tasks.index'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Task $task)
  {
    $this->authorize('delete', $task);

    $task->delete();

    return redirect(route('tasks.index'));
  }
}
