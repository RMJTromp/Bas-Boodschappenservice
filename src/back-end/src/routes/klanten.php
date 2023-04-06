<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Klant;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/klanten", function(Request $request) {
        $klanten = Klant::getAll();
        API::printAndExit($klanten);
    });

    Route::handle(RegExp::compile("/^\/klant\/(\d+)$/"), function(Request $request, array $matches) {
        $id = intval($matches[1][0]);
        $klant = Klant::get($id);

        switch($request->method) {
            case "GET":
                API::printAndExit($klant);
            case "DELETE":
                $klant->delete();
                API::printAndExit([], ResponseCode::OK[0]);
            case "PATCH":
                $_PATCH = $request->body;
                $klant->naam = !empty($_PATCH['naam']) ? $_PATCH['naam'] : $klant->naam;
                $klant->adres = !empty($_PATCH['adres']) ? $_PATCH['adres'] : $klant->adres;
                $klant->postcode = !empty($_PATCH['postcode']) ? $_PATCH['postcode'] : $klant->postcode;
                $klant->woonplaats = !empty($_PATCH['woonplaats']) ? $_PATCH['woonplaats'] : $klant->woonplaats;
                $klant->telefoon = !empty($_PATCH['telefoon']) ? $_PATCH['telefoon'] : $klant->telefoon;
                $klant->save();
                API::printAndExit($klant);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::post("/klant", function(Request $request) {
        try {
            API::printAndExit(Klant::create(
                $request->body['naam'],
                $request->body['adres'],
                $request->body['postcode'],
                $request->body['woonplaats'],
                $request->body['telefoon']));
        } catch(\Exception $e) {
            API::printAndExit($e->getMessage(), $e->getCode());
        }
    });