<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class Controller extends BaseController
{
    // HTTP Status
    const CODE_INTERNAL_ERROR = 500;
    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_NOT_FOUND = 404;
    const CODE_SUCCESS = 200;
    const CODE_CREATED = 201;

    // Paginate config
    const PAGINATE_LIMIT = 10;
    const PAGINATE_MAX_LIMIT = 100;

    // Search config
    const MIN_SEARCH_LEN = 3;

    protected function beforeCreate(Request $request, $obj)
    {
        // Can be override
        return $obj;
    }

    protected function beforeUpdate(Request $request, $obj)
    {
        // Can be override
        return $obj;
    }

    protected function beforeDelete(Request $request, $obj)
    {
        // Can be override
        return $obj;
    }

    /**
     * Create a new Object
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            $obj = $this->model::create($request->all());
            $obj->save();

            return response()->json($obj, self::CODE_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    /**
     * Returns an Object from an ID
     *
     * @param  int  $id
     * @return Response
     */
    public function read(Request $request, $id)
    {
        try {
            $queryBuilder = $this->model::query();

            if (isset($this->withRelationships)) {
                foreach ($this->withRelationships as $table) {
                    $queryBuilder = $queryBuilder->with($table);
                }
            }

            $obj = $queryBuilder->find($id);

            if (!$obj) {
                return response()->json(['error' => 'obj_not_found'], self::CODE_NOT_FOUND);
            }

            return response()->json($obj, self::CODE_SUCCESS);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    /**
     * Update an Object
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $obj = $this->model::find($id);
            if (!$obj) {
                return response()->json(['error' => 'obj_not_found'], self::CODE_NOT_FOUND);
            }

            $obj->fill($request->all());
            $obj = $this->beforeUpdate($request, $obj);
            $obj->push();

            return response()->json(['ok']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    /**
     * Delete an Object
     *
     * @param  Request  $request
     * @return Response
     */
    public function delete($id)
    {
        try {
            $obj = $this->model::find($id);
            if (!$obj) {
                return response()->json(['error' => 'obj_not_found'], self::CODE_NOT_FOUND);
            }

            $obj->delete();
            return response()->json(['ok']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    /**
     * Returns a paginated list of Object. A search criterion can be passed.
     *
     * @param  int  $id
     * @return Response
     */
    public function list(Request $request)
    {
        try {
            $queryBuilder = $this->model::query();

            if (isset($this->withRelationships)) {
                foreach ($this->withRelationships as $table) {
                    $queryBuilder = $queryBuilder->with($table);
                }
            }

            $search = $request->input('search');

            if (!empty($search) && isset($this->searchFields)) {
                if (strlen($search) < self::MIN_SEARCH_LEN) {
                    throw new \Exception('Informe um termo de busca maior que ' . self::MIN_SEARCH_LEN . ' caracteres');
                }
                $queryBuilder = $queryBuilder->where(function ($query) use ($search) {
                    foreach ($this->searchFields as $field) {
                        $query->orWhere($field, 'like', '%' . $search . '%');
                    }
                });
            }

            $result = $this->paginate($request, $queryBuilder);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    /**
     * Recebe uma requisição e um objeto de consulta e trata os parãmetros de paginação,
     * retornando o resultado da consulta paginada.
     *
     * @param  Request $request
     * @param  QueryBuilder $queryBuilder
     * @return array
     */
    protected function paginate(Request $request, QueryBuilder $queryBuilder)
    {
        $limit = $request->input('limit', self::PAGINATE_LIMIT);
        $page = $request->input('page', 1);

        // proteção contra usuário que tentar retornar uma lista muito grande
        $limit = ($limit > self::PAGINATE_MAX_LIMIT) ? self::PAGINATE_MAX_LIMIT : $limit;

        $total = $queryBuilder->count();
        $result = $queryBuilder->skip($limit * ($page - 1))->take($limit)->get();

        return [
            'count' => count($result),
            'total' => $total,
            'results' => $result,
        ];
    }
}
