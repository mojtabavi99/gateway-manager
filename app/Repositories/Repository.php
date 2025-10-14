<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Repository
{
    protected Model $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find a record by id
     */
    public function find($id): Model|Collection|null
    {
        return $this->model->query()->find($id);
    }

    /**
     * Find a record by multiple attributes
     *
     * @param array $conditions ['field' => 'value', ...]
     * @return Model|null
     */
    public function findBy(array $conditions): ?Model
    {
        $query = $this->model->query();

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->first();
    }

    /**
     * Get all records matching conditions
     *
     * @param array $conditions ['field' => 'value', ...]
     * @return Collection
     */
    public function getBy(array $conditions): Collection
    {
        $query = $this->model->query();

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->get();
    }

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        return $this->model->query()->create($data);
    }

    /**
     * Update a record by id
     */
    public function update($id, array $data): Model|Collection|null
    {
        $record = $this->find($id);
        if (!$record) {
            return null;
        }
        $record->update($data);
        return $record;
    }

    /**
     * Delete a record by id
     *
     * @param int $id
     * @param bool $force If true, perform force delete
     * @return bool
     */
    public function delete(int $id, bool $force = false): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }

        // if ($force && method_exists($record, 'forceDelete')) {
        //     return $record->forceDelete();
        // }

        return $record->delete();
    }
}
