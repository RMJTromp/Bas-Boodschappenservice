<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Leverancier;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/leveranciers", function(Request $request) {
        API::printAndExit(Leverancier::getAll());
    });

    Route::handle(RegExp::compile("/^\/leverancier\/(\d+)$/"), function(Request $request, array $matches) {
        $id = intval($matches[1][0]);
        $leverancier = Leverancier::get($id);

        switch($request->method) {
            case "GET":
                API::printAndExit($leverancier);
            case "DELETE":
                $leverancier->delete();
                API::printAndExit([], ResponseCode::OK[0]);
            case "PATCH":
                $_PATCH = $request->body;
                $leverancier->naam = !empty($_PATCH['naam']) ? $_PATCH['naam'] : $leverancier->naam;
                $leverancier->contact = !empty($_PATCH['contact']) ? $_PATCH['contact'] : $leverancier->contact;
                $leverancier->email = !empty($_PATCH['email']) ? $_PATCH['email'] : $leverancier->email;
                $leverancier->adres = !empty($_PATCH['adres']) ? $_PATCH['adres'] : $leverancier->adres;
                $leverancier->postcode = !empty($_PATCH['postcode']) ? $_PATCH['postcode'] : $leverancier->postcode;
                $leverancier->woonplaats = !empty($_PATCH['woonplaats']) ? $_PATCH['woonplaats'] : $leverancier->woonplaats;
                $leverancier->save();
                API::printAndExit($leverancier);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::post("/leverancier", function(Request $request) {
        try {
//            API::printAndExit(Leverancier::create(
//                $request->body['naam'],
//                $request->body['contact'],
//                $request->body['email'],
//                $request->body['adres'],
//                $request->body['postcode'],
//                $request->body['woonplaats']));
        } catch(\Exception $e) {
            API::printAndExit($e->getMessage(), $e->getCode());
        }
    });