<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceNotice extends Model
{
    /**
     * Search maintenance notices.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search = NULL)
    {
        if (!$search) return $query;
        return $query->where('title', 'LIKE', "%$search%")
            ->orWhere('contents', 'LIKE', "%$search%");
    }
}
