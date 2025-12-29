<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
abstract class ApiController extends Controller {

    protected $repository, $indexRequest, $storeRequest, $updateRequest, $result, $params;
    /**
     * Index the resource
     * @return BaseResource
     */
    public function index() : BaseResource {

        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery($this->params)->getResults();
        return $this->getResource();
    }

    /**
     * Store the resource
     * @return BaseResource
     */
    public function store(): BaseResource {

        $this->params = app( $this->storeRequest )->all();
        $this->result = $this->repository->setParameters( $this->params )->create();

        return $this->getResource();
    }
    /**
     * Show the resource
     * @param int $id
     * @return BaseResource
     */
    public function show( int $id ) : BaseResource {
        $this->result = $this->repository->where( 'id', $id );
        return $this->getResource();
    }

    /**
     * Edit the resource
     * @param int $id
     * @return BaseResource
     */
    public function edit( int $id ) : BaseResource {
        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery( $this->params )->where( 'id', $id )->first();
        return $this->getResource();
    }
    /**
     * Update the resource
     * @param int $id
     * @return BaseResource
     */
    public function update( int $id ) : BaseResource {

        $this->params = app( $this->updateRequest )->all();
        $result = $this->repository->where( 'id', $id )->first();
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

    /**
     * Check if the route is public
     * @param string $routeName
     * @return Bool
     */
    public function isPublicRoute(string $routeName): Bool
    {
        $publicRoute = [
            'index', 'view'
        ];
        if (in_array($routeName, $publicRoute)) {
            return true;
        }

        return false;
    }

    /**
     * Get the resource
     * @return BaseResource
     */
    public function getResource(): BaseResource
    {
        return new BaseResource($this->result);
    }

}
