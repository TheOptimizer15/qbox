<?php

namespace App\Common\Repository;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function findAll(int $perPage, array $search = [])
    {
        $filters =$this->filter($search);

        $paginate = $this->perPage($perPage);

        $result = $this->model->where($filters)->paginate($paginate);

        return $this->formatPagination($result);
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data)
    {
        $model->update($data);

        return $this->refresh($model);
    }

    public function refresh(Model $model)
    {
        return $model->refresh();
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }

    public function query()
    {
        return $this->model->query();
    }

    protected function perPage(int $perPage): int
    {
        return min(100, max(10, $perPage));
    }

    protected function formatPagination(Paginator $paginatedData)
    {
        return [
            $paginatedData->items(),
            [
                'per_page' => $paginatedData->perPage(),
                'current_page' => $paginatedData->currentPage(),
                'next_page_url' => $paginatedData->nextPageUrl(),
                'previous_page_url' => $paginatedData->previousPageUrl(),
                'has_more_pages' => $paginatedData->hasMorePages(),
            ],
        ];
    }

    private function filter(array $search)
    {
        return array_filter($search, function ($value) {
            return $value !== '' && $value !== null;
        });

    }
}
