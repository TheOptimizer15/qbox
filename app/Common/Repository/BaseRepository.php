<?php

namespace App\Common\Repository;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseRepository
{
    /**
     * @var TModel
     */
    protected Model $model;

    /**
     * Find all records with pagination and optional filters.
     *
     * @param int $perPage
     * @param array<string, mixed> $search
     * @return array{0: array<int, TModel>, 1: array<string, mixed>}
     */
    public function findAll(int $perPage, array $search = [])
    {
        $filters = $this->filter($search);

        $paginate = $this->perPage($perPage);

        $result = $this->model->where($filters)->paginate($paginate);

        return $this->formatPagination($result);
    }

    /**
     * Find a single record by its primary key.
     *
     * @param int|string $id
     * @return TModel|null
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new record in the database.
     *
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Force create a new record bypassing mass assignment.
     *
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function forceCreate(array $data)
    {
        return $this->model->forceCreate($data);
    }

    /**
     * Update an existing record.
     *
     * @param TModel $model
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function update(Model $model, array $data)
    {
        $model->update($data);

        return $this->refresh($model);
    }

    /**
     * Refresh the model instance from the database.
     *
     * @param TModel $model
     * @return TModel
     */
    public function refresh(Model $model)
    {
        return $model->refresh();
    }

    /**
     * Delete the model from the database.
     *
     * @param TModel $model
     * @return bool|null
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * Get a new query builder for the model.
     *
     * @return Builder<TModel>
     */
    public function query()
    {
        return $this->model->query();
    }

    /**
     * Sanitize the pagination limit.
     *
     * @param int $perPage
     * @return int
     */
    protected function perPage(int $perPage): int
    {
        return min(100, max(10, $perPage));
    }

    /**
     * Format the paginator instance into a standard array structure.
     *
     * @param Paginator $paginatedData
     * @return array{0: array<int, TModel>, 1: array<string, mixed>}
     */
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

    /**
     * Filter out null or empty string search parameters.
     *
     * @param array<string, mixed> $search
     * @return array<string, mixed>
     */
    private function filter(array $search)
    {
        return array_filter($search, function ($value) {
            return $value !== '' && $value !== null;
        });
    }
}