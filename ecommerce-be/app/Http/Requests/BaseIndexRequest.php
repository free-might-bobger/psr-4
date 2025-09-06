<?php

namespace App\Http\Requests;

class BaseIndexRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pickup_store_id'       => 'sometimes',
            'relation'              => 'sometimes',
            'limit'                 => 'sometimes',
            'perPage'               => 'sometimes',
            'page'                  => 'sometimes',
            'filters'               => 'sometimes',
            'type'                  => 'sometimes',
            'with'                  => 'sometimes',
            'address'               => 'sometimes',
            'orderBy'               => 'sometimes',
            'id'                    => 'sometimes',
            'deletedFileIds'        => 'sometimes',
            'store_menu_id'         => 'sometimes',
            'is_visible'            => 'sometimes',
            'whereHas'              => 'sometimes',
            'visibleColumns'        => 'sometimes',
            'only'                  => 'sometimes',
            'franchisee_id'         => 'sometimes',
            'relationNoDecode'      => 'sometimes',
            'citymunCode'           => 'sometimes',
            'isOptimus'             => 'sometimes',
            'name'                  => 'sometimes',
            'end'                   => 'sometimes',
            'start'                 => 'sometimes',
            'trainer_id'            => 'sometimes',
            'days'                  => 'sometimes',
            'firstname'             => 'sometimes',
            'middlename'            => 'sometimes',
            'lastname'              => 'sometimes',
            'email'                 => 'sometimes',
            'mobile'                => 'sometimes',
            'password'              => 'sometimes',
            'roles'                 => 'sometimes',
            'user_id'               => 'sometimes',
            'is_enrollee_default'   => 'sometimes',
            'directions'            => 'sometimes',
            'time_limit'            => 'sometimes',
            'questions'             => 'sometimes',
            'subject_id'            => 'sometimes',
            'selectedSchedules'     => 'sometimes',
            'amount'                => 'sometimes',
            'change'                => 'sometimes',
            'amount_given'          => 'sometimes',
            'enrollee_id'           => 'sometimes',
            'desc'                  => 'sometimes',
            'schedule'              => 'sometimes',
            'optimus_id'            => 'sometimes',
            'login'                 => 'sometimes',
            'logout'                => 'sometimes',
            'task_id'               => 'sometimes',
            'latitude'              => 'sometimes',
            'longitude'             => 'sometimes',
            'radius'                => 'sometimes'
        ];
    }
}
