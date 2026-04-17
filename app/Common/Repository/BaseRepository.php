<?php

namespace App\Common\Repository;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    private function perPage(int $perPage): int
    {
        return min(100, max(10, $perPage));
    }

    public function findAll(int $perPage, $column, $search)
    {
        $paginate = $this->perPage($perPage);
        $result = $this->model->where($column, "%$search%")->simplePaginate($paginate);

        return $result;
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

    public function query(){
        return $this->model->getQuery();
    }
}
