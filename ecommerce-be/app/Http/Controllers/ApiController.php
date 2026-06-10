<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Http\Resources\ShowResource;
abstract class ApiController extends Controller {

    protected string $model;
    protected object $repository;
    protected string $indexRequest;
    protected string $storeRequest;
    protected string $updateRequest;
    protected mixed $result;
    protected array $params;
    protected string $baseResourceClass = BaseResource::class;
    protected string $showResourceClass;
    /**
     * Index the resource
     * @return BaseResource
     */
    public function index() : BaseResource{

        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery($this->params)->getResults();
        return $this->getResource();
    }

    /**
     * Store the resource
     * @return BaseResource|ShowResource
     */
    public function store(): BaseResource|ShowResource {

        $this->params = app( $this->storeRequest )->all();
        $this->result = $this->repository->setParameters( $this->params )->create();

        return $this->getResource();
    }
    /**
     * Show the resource
     * @param int $id
     * @return ShowResource
     */
    public function show( int $id ) : ShowResource {
        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery($this->params)->findOrFail( $id );
        return $this->getShowResource();
    }

    /**
     * Edit the resource
     * @param int $id
     * @return BaseResource|ShowResource
     */
    public function edit( int $id ) : BaseResource|ShowResource {
        $this->params = app( $this->indexRequest )->all();
        $this->result = $this->repository->filterQuery( $this->params )->where( 'id', $id )->first();
        return $this->getResource();
    }
    /**
     * Update the resource
     * @param int $id
     * @return BaseResource|ShowResource
     */
    public function update( int $id ) : BaseResource|ShowResource {
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

  public function getResource(): BaseResource{
    return new $this->baseResourceClass($this->result);
  }

  abstract public function showResourceClass();
}
