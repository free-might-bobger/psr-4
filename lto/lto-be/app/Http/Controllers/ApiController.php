<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
abstract class ApiController extends Controller {

    protected $repository, $indexRequest, $storeRequest, $updateRequest, $result, $params;

    public function index() : BaseResource {

        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery($this->params)->getResults();
        return $this->getResource();
    }

    public function store(): BaseResource {

        $this->params = app( $this->storeRequest )->all();
        $this->result = $this->repository->setParameters( $this->params )->create();

        return $this->getResource();
    }

    public function show( int $id ) : BaseResource {
        $this->result = $this->repository->where( 'id', $id )->first();
        return $this->getResource();
    }

    public function edit( int $id ) : BaseResource {
        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery( $this->params )->where( 'id', $id )->first();
        return $this->getResource();
    }

    public function update( int $id ) : BaseResource {

        $this->params = app( $this->updateRequest )->all();
        $result = $this->repository->where( 'id', $id )->first();
        $this->result = tap( $result )->update( $this->params );
        return $this->getResource();
    }

    public function destroy( int $id ): void {
        $this->result = $this->repository->where( 'id', $id )->delete();
    }

    abstract protected function getResource();

}
