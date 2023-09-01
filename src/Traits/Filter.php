<?php

namespace Phaney\ApiCrud\Traits;

use Closure;

trait Filter
{
    protected array $filters = [];

    public function addFilters(array $filters)
    {
        $this->filters = array_merge($this->columns, $filters);
    }

    public function addFilter($name, Closure $callable)
    {
        $this->filters[$name] = $callable;
    }

    public function applyFilters()
    {
        if (count($this->filters)) {
            foreach($this->filters as $name => $filter) {
                $filterValue = request()->get($name);
                
                if ($filterValue) {
                    $this->query = call_user_func($filter, $filterValue, $this->query);
                }
            }
        }
    }
}