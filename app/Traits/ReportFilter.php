<?php

namespace App\Traits;



trait ReportFilter
{
    public static function scopeApplyDateFilter($query, $filter, $from = null, $to = null)
    {
        return $query->when(isset($from) && isset($to)  && $filter == 'custom', function ($query) use ($from, $to) {
            return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
        })
            ->when($filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when($filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when($filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when($filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            });
        return $query;
    }

    public static function scopeApplyRelationShipSearch($query, $relationships,$searchParameter )
    {
        foreach ($relationships as $relation => $field) {
            $query->orWhereHas($relation, function ($query) use ($field, $searchParameter) {
                $query->where(function ($q) use ($field, $searchParameter) {
                    foreach ($searchParameter as $value) {
                        $q->orWhere($field, 'like', "%{$value}%");
                    }
                });
            });
        }

        return $query;
    }





}
