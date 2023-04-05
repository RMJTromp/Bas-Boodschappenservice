<?php

use Boodschappenservice\core\Request;
use Boodschappenservice\core\Route;
use Boodschappenservice\objects\Verkooporder;
use Boodschappenservice\utilities\API;
use Boodschappenservice\utilities\RegExp;
use Boodschappenservice\utilities\ResponseCode;

Route::get("/verkooporders", function(Request $request) {
    $verkooporders = Verkooporder::getAll();
    API::printAndExit($verkooporders);
});

Route::handle(RegExp::compile("/^\/verkooporder\/(\d+)$/"), function(Request $request, array $matches) {
    $id = intval($matches[1][0]);
    $verkooporder = Verkooporder::get($id);

    switch($request->method) {
        case "GET":
            API::printAndExit($verkooporder);
        case "DELETE":
            $verkooporder->delete();
            API::printAndExit([], ResponseCode::OK[0]);
        case "PATCH":
            $_PATCH = $request->body;
            $verkooporder->klantId = !empty($_PATCH['klantId']) ? $_PATCH['klantId'] : $verkooporder->klantId;
            $verkooporder->artId = !empty($_PATCH['artId']) ? $_PATCH['artId'] : $verkooporder->artId;
            $verkooporder->verkoopAantal = !empty($_PATCH['verkoopAantal']) ? $_PATCH['verkoopAantal'] : $verkooporder->verkoopAantal;
            $verkooporder->verkoopDatum = !empty($_PATCH['verkoopDatum']) ? $_PATCH['verkoopDatum'] : $verkooporder->verkoopDatum;
            $verkooporder->save();
            API::printAndExit($verkooporder);
        default:
            API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
    }
});

Route::post("/verkooporder", function(Request $request) {
    try {
        API::printAndExit(Verkooporder::create(
            $request->body['klantId'],
            $request->body['artId'],
            $request->body['verkoopAantal'],
            $request->body['verkoopDatum']));
    } catch(\Exception $e) {
        API::printAndExit($e->getMessage(), $e->getCode());
    }
});
