<?php

use Boodschappenservice\core\Request;
use Boodschappenservice\core\Route;
use Boodschappenservice\objects\Inkooporder;
use Boodschappenservice\utilities\API;
use Boodschappenservice\utilities\RegExp;
use Boodschappenservice\utilities\ResponseCode;

Route::get("/inkooporders", function(Request $request) {
    $inkooporders = Inkooporder::getAll();
    API::printAndExit($inkooporders);
});

Route::handle(RegExp::compile("/^\/inkooporder\/(\d+)$/"), function(Request $request, array $matches) {
    $inkoopId = intval($matches[1][0]);
    $inkooporder = Inkooporder::get($inkoopId);

    switch($request->method) {
        case "GET":
            API::printAndExit($inkooporder);
        case "DELETE":
            $inkooporder->delete();
            API::printAndExit([], ResponseCode::OK[0]);
        case "PATCH":
            $_PATCH = $request->body;
            $inkooporder->artId = !empty($_PATCH['artId']) ? intval($_PATCH['artId']) : $inkooporder->artId;
            $inkooporder->levId = !empty($_PATCH['levId']) ? intval($_PATCH['levId']) : $inkooporder->levId;
            $inkooporder->inkoopAantal = !empty($_PATCH['inkoopAantal']) ? intval($_PATCH['inkoopAantal']) : $inkooporder->inkoopAantal;
            $inkooporder->inkoopDatum = !empty($_PATCH['inkoopDatum']) ? $_PATCH['inkoopDatum'] : $inkooporder->inkoopDatum;
            $inkooporder->save();
            API::printAndExit($inkooporder);
        default:
            API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
    }
});

Route::post("/inkooporder", function(Request $request) {
    API::printAndExit(Inkooporder::create(
        intval($request->body['artId']),
        intval($request->body['levId']),
        intval($request->body['inkoopAantal']),
        $request->body['inkoopDatum']));
});
