<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'order',
        'lists_id',
    ];



    public function list(): BelongsTo
    {
        return $this->belongsTo(Lists::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
