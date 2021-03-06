<?php

namespace Devsbuddy\AdminrCore\Traits;

trait HasExcludeScope
{
    /**
     * Scope a query to only exclude specific Columns.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExclude($query, ...$columns)
    {
        if ($columns !== []) {
            if (count($columns) !== count($columns, COUNT_RECURSIVE)) {
                $columns = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($columns)));
            }

            return $query->select(array_diff($this->getTableColumns(), $columns));
        }
        return $query;
    }

    /**
     * Shows All the columns of the Corresponding Table of Model
     *
     * @author Manojkiran.A <manojkiran10031998@gmail.com>
     * If You need to get all the Columns of the Model Table.
     * Useful while including the columns in search
     * @return array
     **/
    public function getTableColumns()
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('MigrMod:' . filemtime(database_path('migrations')), function () {
            return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        });
    }
}
