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

    Route::handle(RegExp::compile("/^\/Klant\/(\d+)$/"), function(Request $request, array $matches) {
        $id = intval($matches[1][0]);
        $Klant = Klant::get($id);

        switch($request->method) {
            case "GET":
                API::printAndExit($Klant);
            case "DELETE":
                $Klant->delete();
                API::printAndExit([], ResponseCode::OK[0]);
            case "PATCH":
                $_PATCH = $request->body;
                $Klant->naam = !empty($_PATCH['naam']) ? $_PATCH['naam'] : $Klant->naam;
                $Klant->adres = !empty($_PATCH['adres']) ? $_PATCH['adres'] : $Klant->adres;
                $Klant->postcode = !empty($_PATCH['postcode']) ? $_PATCH['postcode'] : $Klant->postcode;
                $Klant->woonplaats = !empty($_PATCH['woonplaats']) ? $_PATCH['woonplaats'] : $Klant->woonplaats;
                $Klant->telefoon = !empty($_PATCH['telefoon']) ? $_PATCH['telefoon'] : $Klant->telefoon;
                $Klant->save();
                API::printAndExit($Klant);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::post("/Klant", function(Request $request) {
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