<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Http\Resources\ShowResource;
abstract class ApiController extends Controller {

    protected string $model;
    protected object $repository;
    protected string $indexRequest;
    protected string $showRequest;
    protected string $storeRequest;
    protected string $updateRequest;
    protected mixed $result;
    protected array $params;
    protected string $baseResourceClass;
    protected string $showResourceClass;

    public function index() {

        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery($this->params)->getResults();
        return $this->getResource();
    }

    public function store(){

        $this->params = app( $this->storeRequest )->all();
        $this->result = $this->repository->setParameters( $this->params )->create();

        return $this->getResource();
    }
    /**
     * Show the resource
     * @param int $id
     */
    public function show( int $id ) {
        $this->params = app( $this->showRequest )->all();
        $this->result = $this->repository->filterQuery($this->params)->findOrFail( $id );
        return $this->showResource();
    }

    /**
     * Edit the resource
     * @param int $id
     */
    public function edit( int $id ){
        $this->params = app( $this->editRequest )->all();
        $this->result = $this->repository->filterQuery( $this->params )->where( 'id', $id )->first();
        return $this->getResource();
    }
    /**
     * Update the resource
     * @param int $id
     */
    public function update( int $id ) {
        $this->params = app( $this->updateRequest )->all();
        $result = $this->repository->where( 'id', $id );
        $this->result = tap( $result )->update( $this->params );
        return $this->getResource();
    }

    /**
     * Destroy the resource
     * @param int $id
     * @return void
     */
    public function destroy( int $id ): void {
        $this->result = $this->repository->where( 'id', $id )->delete();
    }

    public function restore($id): void {
        $this->result = $this->repository->where('id', $id)->restore();
    }

  abstract public function getResource();

  abstract public function showResource();
}
