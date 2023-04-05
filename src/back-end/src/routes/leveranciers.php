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
        $_GET = $request->url->searchParams;
        $_POST = $request->body;


        $amount = $request->url->searchParams["amount"] ?? 1;
        if($amount < 1) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at least 1");
        else if($amount > 100) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at most 100");

        if($amount === 1) {
            $leverancier = !isset($_GET['random']) ? Leverancier::generateRandom() : Leverancier::create();

            if(!empty($_POST['naam'])) $leverancier->naam = $_POST['naam'];
            if(!empty($_POST['contact'])) $leverancier->contact = $_POST['contact'];
            if(!empty($_POST['email'])) $leverancier->email = $_POST['email'];
            if(!empty($_POST['adres'])) $leverancier->adres = $_POST['adres'];
            if(!empty($_POST['postcode'])) $leverancier->postcode = $_POST['postcode'];
            if(!empty($_POST['woonplaats'])) $leverancier->woonplaats = $_POST['woonplaats'];
            $leverancier->save();

            API::printAndExit($leverancier);
        } else {
            $leveranciers = [];
            for($i = 0; $i < $amount; $i++) {
                $leveranciers[] = Leverancier::generateRandom();
            }
            API::printAndExit($leveranciers);
        }
    });