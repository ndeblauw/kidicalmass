<?php

namespace App\Models\Scopes;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class LocalGroupScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->ray();
        if (! request()->is('admin*')) {
            return;
        }

        $user = auth()->user();

        if (! $user) {
            return;
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $isCaptain = DB::table('group_user')
            ->where('user_id', $user->getKey())
            ->where('role', 'captain')
            ->exists();

        if (! $isCaptain) {
            abort(404);
        }

        // Scope the query to the captain's groups
        $groupIds = DB::table('group_user')
            ->where('user_id', $user->getKey())
            ->where('role', 'captain')
            ->pluck('group_id');

        if ($model instanceof Group) {
            $builder->whereKey($groupIds);
        } elseif (method_exists($model, 'groups')) {
            $builder->whereHas('groups', fn (Builder $q) => $q->whereKey($groupIds));
        } elseif ($model->isRelation('group') && $model->group() instanceof BelongsTo) {
            $builder->whereIn($model->group()->getForeignKeyName(), $groupIds);
        }
    }
}
