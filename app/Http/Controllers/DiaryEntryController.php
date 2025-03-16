<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class DiaryEntryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    $query = DiaryEntry::where('user_id', Auth::id());

    // 検索フィルター
    if ($request->search) {
      $query->where('content', 'like', '%' . $request->search . '%');
    }

    // タグフィルター
    if ($request->tags && is_array($request->tags) && count($request->tags) > 0) {
      $query->where(function ($query) use ($request) {
        foreach ($request->tags as $tag) {
          $query->whereJsonContains('tags', $tag);
        }
      });
    }

    // 気分フィルター
    if ($request->mood) {
      $query->where('mood', $request->mood);
    }

    $entries = $query->orderBy('entry_date', 'desc')->get();

    // 月ごとにグループ化
    $entriesByMonth = $entries->groupBy(function ($entry) {
      return $entry->entry_date->format('Y-m');
    });

    // デフォルトのタグ一覧
    $defaultTags = [
      '仕事',
      '家族',
      '趣味',
      '健康',
      '運動',
      '食事',
      '睡眠',
      '学習',
      '恋愛',
      '読書',
      '映画',
      '音楽',
      '旅行',
      '買い物',
      'ペット',
      '天気',
      'イベント',
      '目標',
      '成果',
      '反省',
      '感謝',
      'アイデア',
      '夢',
      '思い出',
      '計画',
      '習慣'
    ];

    // 既存のタグと結合して重複を除去
    $availableTags = array_values(array_unique(array_merge(
      $defaultTags,
      DiaryEntry::where('user_id', Auth::id())
        ->whereNotNull('tags')
        ->get()
        ->pluck('tags')
        ->flatten()
        ->toArray()
    )));

    // 気分の統計
    $moodStats = DiaryEntry::where('user_id', Auth::id())
      ->whereNotNull('mood')
      ->select('mood', DB::raw('count(*) as count'))
      ->groupBy('mood')
      ->get();

    return Inertia::render('DiaryEntries/Index', [
      'entries' => $entriesByMonth,
      'availableTags' => $availableTags,
      'moodStats' => $moodStats,
      'filters' => [
        'search' => $request->search,
        'tags' => $request->tags ?? [],
        'mood' => $request->mood,
      ],
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
      'content' => 'required|string',
      'mood' => 'nullable|string|max:255',
      'completed_tasks' => 'nullable|array',
      'tags' => 'nullable|array',
      'entry_date' => 'required|date',
    ]);

    $validated['user_id'] = Auth::id();

    DiaryEntry::create($validated);

    return redirect()->route('diary-entries.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(DiaryEntry $diaryEntry): Response
  {
    $this->authorize('view', $diaryEntry);

    return Inertia::render('DiaryEntries/Show', [
      'entry' => $diaryEntry
    ]);
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
  public function update(Request $request, DiaryEntry $diaryEntry)
  {
    if (!auth()->user()->can('update', $diaryEntry)) {
      abort(403);
    }

    // 部分更新のためのバリデーションルール
    $rules = [];
    if ($request->has('content')) {
      $rules['content'] = 'required|string';
    }
    if ($request->has('mood')) {
      $rules['mood'] = 'nullable|string|max:255';
    }
    if ($request->has('completed_tasks')) {
      $rules['completed_tasks'] = 'nullable|array';
    }
    if ($request->has('tags')) {
      $rules['tags'] = 'nullable|array';
    }
    if ($request->has('entry_date')) {
      $rules['entry_date'] = 'required|date';
    }

    $validated = $request->validate($rules);
    $diaryEntry->update($validated);

    return redirect()->route('diary-entries.index');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(DiaryEntry $diaryEntry)
  {
    $this->authorize('delete', $diaryEntry);

    $diaryEntry->delete();

    return redirect()->route('diary-entries.index');
  }
}
