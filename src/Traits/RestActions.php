<?php namespace Olivion\RestGen\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * trait for all REST actions
 * controller must have constant with model's name
 * model must have public static $rules array to validate input data
 */
trait RestActions{    
    public function all(Request $request): mixed{
        if($request->filled('limit')) return response()->json(self::MODEL::limit($request->limit)->get(),200);
        return response()->json(self::MODEL::all(),200);
    }

    public function get(int $id): mixed{
        return response()->json(self::MODEL::find($id),200);
    }

    public function create(Request $request): mixed{
        $this->validate($request,self::MODEL::$rules);
        $fields = Arr::only($request->all(),array_keys(self::MODEL::$rules));
        if($request->filled('id')) $fields['id'] = $request->id;
        if($model = self::MODEL::where($fields)->first()){
            $model->update($fields);
            return response()->json($model,201);
        }
        return response()->json(self::MODEL::create($fields),201);
    }

    public function update(Request $request, int $id): mixed{
        $this->validate($request,self::MODEL::$rules);
        $model = self::MODEL::find($id);
        $model->update($request->all());
        return response()->json($model,200);
    }

    public function delete(int $id): mixed{
        self::MODEL::destroy($id);
        return response()->json(self::MODEL.' with id: '.$id.' removed',200);
    }
}