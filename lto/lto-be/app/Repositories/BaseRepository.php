<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Support\FieldSupport;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Support\Arr;
use App\Traits\Support\BaseSupportRepository;
use App\Models\Image;
use App\Traits\UtilsTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
class BaseRepository implements BaseInterface
{
    use UtilsTrait, FieldSupport, OptimusRequiredToModel, BaseSupportRepository;

    protected $model, $params, $cacheKey;

    public function setModel($model): self{
        $this->model = $model;
        return $this;
    }

    //filters=id:1,name:as
    public function filterQuery(array $parameters):self
    {
        $this->setParameters($parameters);
        $filters = $this->pregSplit('@,@', Arr::get( $parameters, 'filters', ''));
        foreach ($filters as $filterKeys => $filterValues) {
            [$column, $value] = $this->pregSplit('@:@', $filterValues);
            if (method_exists($this, $column)) {
                call_user_func([$this, $column], $value);
            }
        }
        $this->with();
        return $this;
    }

     //type=listing
     public function getResults(): LengthAwarePaginator|Collection
     {
         $limit = Arr::get($this->params, 'limit', 12);
         $type  = Arr::get($this->params, 'type', false);
         if ($type) {
             return $this->model->take($limit)->get();
         }
         return $this->model->paginate($limit);
     }

     public function setParameters(array $parameters):self{
        $this->params = $parameters;
        return $this;
     }

     public function where(int $optimusId) {
        return $this->model->where($this->optimus()->decode($optimusId))->first();
    }

    public function setFillable(): void
    {
        $this->fillable = $this->model->getFillable();
    }


     /***
      * NOT YET REWRITTEN BELOW
      NOT YET REWRITTEN BELOW
      NOT YET REWRITTEN BELOW
      */

    /**
     * Filters query parameters by invoking methods named after each key in the parameter array.
     * 
     * This function iterates through `$this->params`, where each key-value pair represents 
     * a specific query constraint. If a method exists within this class that matches a key, 
     * it dynamically calls that method, passing the corresponding value as an argument.
     * 
     * Example:
     * Given `$this->params = ['user_id' => 27]`, this function will call `$this->user_id(27)`, 
     * assuming a `user_id` method exists in the class.
     * Please check App/Support/FieldSupport for all global filter
     * if you need specific filter you can create in your OwnRepository
     * public function key($value)
     *
     * @return $this
     */
    // public function filterQuery(array $request): self
    // {
    //     foreach ($request as $key => $param) {
    //         if(method_exists($this, $key)){
    //             call_user_func([$this, $key], $param);
    //         }
    //     }

    //     return $this->getResults();
    // }

    //orderBy=name:asc
    public function orderBy($param)
    {
        [$column, $value] = $this->pregSplit('@:@', $param);
        $this->model = $this->model->orderBy($column, $value);
        return $this;
    }

    public function limit()
    {
        $limit = Arr::get($this->params, 'limit', 15);
        $this->model = $this->model->limit($limit);
        return $this;
    }

    //type=collection
    public function get()
    {
        if (Arr::get($this->params, 'type', null) === 'collection') {
            return $this->model->get();
        }
        return $this->model->paginate(Arr::get($this->params, 'limit', 100));
    }

    //&columns=id,name
    public function columns()
    {
        $this->model = $this->model->select($this->pregSplit('@,@',  Arr::get( $this->params, 'columns', []) ));
        return $this;
    }

    //&with=itemPrice.unit:id;<;20,images:id;1
    public function with()
    {
        
        $relationships = $this->pregSplit('@,@', Arr::get( $this->params, 'with') );
        foreach($relationships as $relationship){
            
            $pregSplit = $this->pregSplit('@:@', $relationship);
            if(count($pregSplit) > 1){
                [$relationTable, $fieldConditionValue] = $this->pregSplit('@:@', $relationship);

                $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
                if (count($fieldConditionValueArray) === 3) {
                    [$field, $comparison, $value] = $fieldConditionValueArray;
                    $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $this->optimus()->decode($value));
                } else {
                    
                    [$field, $value] = $fieldConditionValueArray;
                    
                    $this->model = $this->model->with([$relationTable => function($q) use ($field, $value) {
                        if(Arr::get( $this->params, 'isOptimus') === 'false' ){
                            return $q->where($field, $value);
                        }
                        return $q->where($field, $this->optimus()->decode($value));
                       
                    }]);
                }
               
            }else{
                $this->model = $this->model->with($relationship);
            }
            
            
        }
        
        return $this;
    }

    //relation=itemPrice:price;<;1439664,images:id;1439664
    //optimus value
    public function relation()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'relation', []));
        foreach ($relationships as $relationship) {
            [$relationTable, $fieldConditionValue] = $this->pregSplit('@:@', $relationship);
            $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
            if (count($fieldConditionValueArray) === 3) {
                
                [$field, $comparison, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $this->optimus()->decode($value));
            } else {
                
                [$field, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $this->optimus()->decode($value));
            }
        }

        return $this;
    }

    //relationNoDecode=itemPrice:price;in;1439664,images:id;>;1439664
    //realfield value
    public function relationNoDecode()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'relationNoDecode', []));
        foreach ($relationships as $relationship) {
            [$relationTable, $fieldConditionValue] = $this->pregSplit('@:@', $relationship);
            $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
            if (count($fieldConditionValueArray) === 3) {
                [$field, $comparison, $value] = $fieldConditionValueArray;
                if($comparison == 'in'){
                    $explode = explode('*', $value);
                    $this->model = $this->model->whereRelation($relationTable, fn($q) => $q->whereIn($field, $explode));
                }
                else if( $comparison == 'like'){
                    $this->model = $this->model->whereRelation($relationTable, fn($q) => $q->where($field, 'LIKE' , '%' . $value . '%'));
                }
                else{
                    $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $value);
                } 
               
            } else {
                
                [$field, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $value);
            }
        }

        return $this;
    }

    /**
     * This will also works in many to many relationship
     * whereHas=itemPrice:price;1.2.3,images:id;1.2.3
     * Do not change this it is confirm on 7-21-2024
     * By Bobby Gerez
     */
    public function whereHas()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'whereHas', []));
        
        foreach ($relationships as $relationship) {
            [$relationTable, $fieldValue] = $this->pregSplit('@:@', $relationship);
            $fieldValueArray = $this->pregSplit('@;@', $fieldValue);
            if (count($fieldValueArray) === 2) {
                [$field, $value] = $fieldValueArray;

                $this->model = $this->model->whereHas(
                    $relationTable,
                    function(Builder $query) use ($field, $value) {
                        $explode = explode('.', $value);
                        $query->whereIn($field, $explode);
                    }
                );
                
            }
        }

        return $this;
    }

    //Get the associated Class ex. items
    // morph=imageable:field;like;asd
    public function morph()
    {
        [$associated, $queries] = $this->pregSplit('@:@', $this->request->morph);
        $queries = $this->pregSplit('@;@', $queries);
        if (count($queries) === 3) {
            [$field, $operator, $value] = $queries;
            $this->model = $this->model->whereHasMorph(
                $associated,
                $this->associatedClass,
                function (Builder $query) use ($field, $operator, $value) {
                    $query->where($field, $operator, '%' . $value . '%');
                }
            );
        } else {
            [$field, $value] = $queries;
            $this->model = $this->model->whereHasMorph(
                $associated,
                $this->associatedClass,
                function (Builder $query) use ($field, $value) {
                    $query->where($field, $value);
                }
            );
        }
        return $this;
    }

    

    public function like($column, $value):self
    {
        $this->model = $this->model->where($column, 'LIKE', '%' . $value . '%');
        return $this;
    }

    public function first()
    {
        return $this->model->first();
    }

    public function findOrFail($id)
    {
        $this->model = $this->model->findOrFail($this->optimus()->decode($id));
        return $this->model;
    }

    public function create()
    {
        return $this->model->create($this->params);
    }

    public function update()
    {

        $data = array_intersect_key(
            $this->request->all(),
            array_flip($this->fillable)
        );

        return $this->model->first()->update($data);
    }

    public function delete()
    {
        return $this->model->delete();
    }

    public function upload()
    {

        if (isset($_FILES["images"])) {
           

            foreach ($_FILES["images"]['name'] as $key => $value) {

                $this->name = $_FILES["images"]['name'][$key];
                $this->fileName = time() . '-' . $this->name;
                $fileTmp = $_FILES["images"]['tmp_name'][$key];
                $this->size = $_FILES["images"]['size'][$key];
                $uploadfile = file_get_contents($fileTmp);

                \File::put(public_path() . '/images/uploads/' . $this->fileName , $uploadfile);

                if ($this->request->isPrimary && $this->model->image) {
                    foreach($this->model->images as $image){
                        $image->update([
                            'is_primary' => 0
                        ]);
                    }

                    if($this->name == $this->request->primaryName){
                        $this->imageUploadIsPrimary(true);
                    }else{
                        $this->imageUploadIsPrimary(false);
                    }
                    

                }else{
                    if($key == 0){
                        $this->imageUploadIsPrimary(true);
                    }else{
                        $this->imageUploadIsPrimary(false);
                    }
                }
            }
        }
        
    }

   

    public function all(){
        return $this->model->all();
    }

    public function noDecodeWhere($field, $condition, $value){
        return $this->model->where($field, $condition, $value);
    }

    public function decodeWhere($field, $value){
        return $this->model->where($field, $this->optimus()->decode($value));
    }

    public function filesUpload()
    {
        $request = app()->make('request');
        if (isset($_FILES["files"])) {

            foreach ($this->model->first()->images as $image) {
                $image->update(['is_primary' => 0]);
            }

            foreach ($_FILES["files"]['name'] as $key => $value) {

                $this->name = $_FILES["files"]['name'][$key];
                $this->fileName = time() . '-' . $this->name;
                $fileTmp = $_FILES["files"]['tmp_name'][$key];
                $this->size = $_FILES["files"]['size'][$key];
                $uploadfile = file_get_contents($fileTmp);
                \File::put(public_path() . '/images/uploads/' . $this->fileName , $uploadfile);

                $image = new Image([
                    'thumbnail' => 'images/uploads/' . $this->fileName,
                    'path' => 'images/uploads/' . $this->fileName,
                    'name'  => $this->name,
                    'is_primary' => $request->primaryImageName == $this->name,
                    'size'  => $this->size
                ]);

                $this->model->first()->images()->save($image);

            }

           
        }
        $this->deletedFiles();
        
    }

    protected function deletedFiles(){
        $request = app()->make('request');
        if ($request->deletedFiles) {
            foreach ($request->deletedFiles as $id) {
                $image = Image::find($id);
                if ($image) {
                    $path = public_path($image->path);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $image->delete();
                }
            }
        }
    }

}
