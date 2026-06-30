<?php

namespace App\Repositories;

use App\Models\StoreUser;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusId; 

class StoreUserRepository extends BaseRepository
{
    use OptimusId;
    
    public function __construct()
    {
        $this->getModel();
        $this->cacheKey = 'StoreMenus-get';
    }

    public function getModel(): Model {
        $this->model = new StoreUser;
        return $this->model;
    }

    /**
     * Invite a user to a store
     * 
     * @param array $data
     * @return void
     */
    public function inviteUser(array $data){

        if ( $this->model->where('store_id', $this->optimus()->decode($data['store_id']))->where('email', $data['email'])->exists() ) {
            throw new \Exception('User already invited to this store');
        }
        $this->model->create([
            'store_id' => $this->optimus()->decode($data['store_id']),
            'email' => $data['email'],
            'verification_code' => uniqid(),
            'is_verified' => false
        ]);

    }

}
