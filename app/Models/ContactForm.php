<?php

namespace App\Models;

use App\Models\Scopes\LocalGroupScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([LocalGroupScope::class])]
#[Fillable('name', 'email', 'message', 'phone', 'page_url', 'honeypot', 'group_id', 'handled_at')]
class ContactForm extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeUnhandled($query)
    {
        return $query->whereNull('handled_at');
    }
}
