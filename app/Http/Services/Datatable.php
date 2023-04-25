<?php

namespace App\Http\Services;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Datatable
 * @package App\Http\Services
 */
class Datatable
{
    /**
     * @param Builder $data
     * @param $filters
     * @return array
     */
    public static function process(Builder $data, $filters, $relationalFilters = []) {
        if (isset($filters['redirectFilter'])) {
            foreach ($filters['redirectFilter'] as $k => $filter)
                $data->where($k, $filter);
        } else
            $data->where(function($query) use ($filters, $relationalFilters) {
                foreach ($filters['columns'] as $k => $column)
                    if (isset($column['searchable']) && $column['searchable'] && isset($filters['search']) && $filters['search'] != '')
                        $query->orWhere($column['selector'], 'LIKE', "%{$filters['search']}%");


                foreach ($relationalFilters as $filter) {
                    $query->whereDoesntHave($filter);
                    $query->orWhereHas($filter, function ($q) use ($filters, $filter) {
                        $q->where(substr($filter, 0, -1), 'like', "%{$filters['search']}%");
                    });
                }
            });

        //Order our columns
        foreach ($filters['columns'] as $column)
            if (isset($column['sort']) && $column['sort']['direction'])
                $data->orderBy($column['selector'], $column['sort']['direction']);

        //Handle our pagination
        $pages = $data->paginate($filters['pages']['resultsPerPage'], ['*'], 'page', $filters['pages']['current']);

        $filters['totalResults'] = $pages->total();
        $filters['pages']['total'] = $pages->total() / $filters['pages']['resultsPerPage'];

        return [
            'filters' => $filters,
            'data' => $data->get()
        ];
    }
}

