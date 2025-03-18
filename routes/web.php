<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\DarkModeController;
use App\Models\Task;
use App\Models\DiaryEntry;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ルートパスへのアクセスをダッシュボードにリダイレクト
Route::get('/', function () {
  return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/dashboard', function () {
    // 最近のタスク（5件）
    $recentTasks = Task::where('user_id', auth()->id())
      ->orderBy('created_at', 'desc')
      ->limit(5)
      ->get();

    // 期限が近い未完了のタスク（5件）
    $upcomingTasks = Task::where('user_id', auth()->id())
      ->where('completed', false)
      ->whereNotNull('due_date')
      ->orderBy('due_date', 'asc')
      ->limit(5)
      ->get();

    // 最近の日記（5件）
    $recentEntries = DiaryEntry::where('user_id', auth()->id())
      ->orderBy('entry_date', 'desc')
      ->limit(5)
      ->get();

    // 月ごとの日記数の統計
    $entriesByMonth = DiaryEntry::where('user_id', auth()->id())
      ->selectRaw('DATE_FORMAT(entry_date, "%Y-%m") as month, COUNT(*) as count')
      ->groupBy('month')
      ->orderBy('month', 'desc')
      ->limit(6)
      ->get();

    // 未完了のタスク数
    $incompleteTasksCount = Task::where('user_id', auth()->id())
      ->where('completed', false)
      ->count();

    // 完了したタスク数
    $completedTasksCount = Task::where('user_id', auth()->id())
      ->where('completed', true)
      ->count();

    return Inertia::render('Dashboard', [
      'recentTasks' => $recentTasks,
      'upcomingTasks' => $upcomingTasks,
      'recentEntries' => $recentEntries,
      'entriesByMonth' => $entriesByMonth,
      'taskStats' => [
        'incomplete' => $incompleteTasksCount,
        'completed' => $completedTasksCount
      ]
    ]);
  })->name('dashboard');

  Route::resource('tasks', TaskController::class);
  Route::resource('diary-entries', DiaryEntryController::class);

  Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  });

  Route::patch('/dark-mode', [DarkModeController::class, 'update'])->name('dark-mode.update');
});

require __DIR__ . '/auth.php';
