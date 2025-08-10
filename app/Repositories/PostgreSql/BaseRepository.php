<?php

namespace App\Repositories\PostgreSql;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Criteria\QueryParameters;
use Fukibay\StarterPack\Traits\AppliesQueryCriteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    
    use AppliesQueryCriteria;

    protected Model $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /* ===================== CRUD & Entry Points ===================== */

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function get(?QueryParameters $criteria = null): Collection
    {
        $query = $this->applyCriteria($criteria);
        return $query->get($criteria?->columns ?? ['*']);
    }

    public function paginate(
        ?QueryParameters $criteria = null,
        int $perPage = 15,
        string $pageName = 'page',
        ?int $page = null
    ): LengthAwarePaginator {
        $query = $this->applyCriteria($criteria);
        return $query->paginate($perPage, $criteria?->columns ?? ['*'], $pageName, $page);
    }

    public function findBy(?QueryParameters $criteria = null): ?Model
    {
        $query = $this->applyCriteria($criteria);
        return $query->first($criteria?->columns ?? ['*']);
    }

    public function findByOrFail(?QueryParameters $criteria = null): Model
    {
        $query = $this->applyCriteria($criteria);
        return $query->firstOrFail($criteria?->columns ?? ['*']);
    }

    public function findById(int $id, array $relations = [], array $columns = ['*']): ?Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    public function findByIdOrFail(int $id, array $relations = [], array $columns = ['*']): Model
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->findByIdOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): void
    {
        $record = $this->findByIdOrFail($id);
        $record->delete();
    }
}
