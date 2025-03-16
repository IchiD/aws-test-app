<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiaryEntry extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'content',
    'mood',
    'completed_tasks',
    'tags',
    'entry_date',
  ];

  protected $casts = [
    'completed_tasks' => 'array',
    'tags' => 'array',
    'entry_date' => 'date',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
