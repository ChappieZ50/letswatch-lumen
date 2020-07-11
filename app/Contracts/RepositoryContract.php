<?php

namespace App\Contracts;


interface RepositoryContract
{

    public function __construct();

    public function all();

    public function get($columns = ['*']);

    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null);

    public function find($id, $columns = ['*']);

    public function findOrFail($id, $columns = ['*']);

    public function store(array $data);

    public function update(array $data, $id);

    public function destroy($id);

    public function exists($id);
}