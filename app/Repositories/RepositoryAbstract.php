<?php


namespace App\Repositories;


use App\Contracts\RepositoryContract;
use Exception;

abstract class RepositoryAbstract implements RepositoryContract
{

    protected $entity;

    protected $primaryKey;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->entity = $this->resolveEntity();
        $this->primaryKey = $this->entity->getKeyName();
    }


    /**
     * @throws Exception
     */
    protected function resolveEntity()
    {
        if (!method_exists($this, 'entity')) {
            throw new Exception('Entity method not exists');
        }
        return app()->make($this->entity());
    }


    public function all()
    {
        return $this->entity->all();
    }


    public function get($columns = ['*'])
    {
        return $this->entity->get($columns);
    }

    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->entity->paginate($perPage, $columns, $pageName, $page);
    }


    public function find($id, $columns = ['*'])
    {
        return $this->entity->find($id, $columns);
    }

    public function findOrFail($id, $columns = ['*'])
    {
        return $this->entity->findOrFail($id, $columns);
    }


    public function store(array $data)
    {
        return new $this->entity($data);
    }


    public function update(array $data, $id)
    {
        $find = $this->entity->where($this->primaryKey, $id);
        return $find->update($data) ? $find : false;
    }


    public function destroy($id)
    {
        return $this->entity->findOrFail($id)->delete();
    }


    public function exists($id)
    {
        return $this->entity->where($this->primaryKey, $id)->exists();
    }


}