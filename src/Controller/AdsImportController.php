<?php

namespace App\Controller;

use App\Models\Ads;
use Shuchkin\SimpleXLSX;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AdsImportController
{
    public function import(Request $req, Response $res)
    {
        $files = $req->getUploadedFiles();

        if (!isset($files['xlsx']) || $files['xlsx']->getError() !== UPLOAD_ERR_OK) {
            return $res->withStatus(400, 'Invalid file upload');
        }

        $xlsx = $files['xlsx'];
        $parsedBody = $req->getParsedBody();
        $fileProcessingMode = isset($parsedBody['fileProcessingMode']) ? filter_var($parsedBody['fileProcessingMode'], FILTER_VALIDATE_BOOLEAN) : false;
        $parsed = SimpleXLSX::parse($xlsx->getFilePath());

        Ads::parse($parsed,$fileProcessingMode);

        $res->getBody()->write("Upload successful\n");
        return $res;
    }
}
