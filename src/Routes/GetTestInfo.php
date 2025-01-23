<?php

namespace App\Routes;

use App\Controller\RestController;
use App\Models\Test;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetTestInfo extends RestController
{
    public function action(): ResponseInterface
    {
        try {

            $model = Test::findOne(1);
            return $this->respondWithData($model['name']);

        } catch (Throwable $e) {
            return $this->respondWithError(253, $e->getMessage());
        }
    }

}