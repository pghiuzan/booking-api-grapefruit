<?php

namespace App\Services;

use App\DTO\TripsFilterDTO;
use App\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TripsFilterService
{
    public function filterTrips(TripsFilterDTO $filter, int $pageSize, int $page = 1): LengthAwarePaginator
    {
        $query = Trip::query();

        if ($filter->has('search')) {
            $searchKeywords = array_map(function($keyword) {
                return strtolower($keyword);
            }, explode(' ', $filter->search));

            $query->where(function($q) use ($searchKeywords) {
                foreach ($searchKeywords as $keyword) {
                    $q->orWhere(DB::raw('lower(title)'), 'like', sprintf('%%%s%%', $keyword))
                          ->orWhere(DB::raw('lower(description)'), 'like', sprintf('%%%s%%', $keyword));
                }
            });
        }

        $orderBy = $filter->has('orderBy') ? $filter->orderBy : 'created_at';
        $orderDirection = $filter->has('orderDirection') ? $filter->orderDirection : 'asc';
        $query->orderBy($orderBy, $orderDirection);

        if ($filter->has('minPrice') && is_numeric($filter->minPrice)) {
            $query->where('price', '>=', $filter->minPrice);
        }

        if ($filter->has('maxPrice') && is_numeric($filter->maxPrice)) {
            $query->where('price', '<=', $filter->maxPrice);
        }

        return $query->paginate($pageSize, ['*'], 'trips', $page);
    }
}
