<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Klant;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\ArrayList;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/klanten", function() {
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        $limit = min(max($limit, 1), 100);
        $offset = max($offset, 0);

        $search = strtolower($_GET['search'] ?? "");
        $treshold = floatval($_GET['treshold'] ?? 0.5);

        $klanten = new ArrayList(Klant::getAll());

        if(!empty($search)) {
            $searchLen = strlen($search);
            $klanten = $klanten
                ->map(function(Klant $klant) use ($search, $searchLen) {
                    $naam = strtolower($klant->naam);
                    $match = 1 - (levenshtein($naam, $search) / max(strlen($naam), $searchLen));
                    if(str_contains($naam, $search)) $match = ($match + 1) / 2;
                    return [
                        'object' => $klant,
                        'match' => $match
                    ];
                })
                ->sort(fn(array $a, array $b) => $b['match'] <=> $a['match'])
                ->filter(fn(array $a) => $a['match'] > $treshold)
                ->map(fn(array $a) => $a['object']);
        }

        API::printAndExit($klanten->slice($offset, $limit));
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
                $klant->email = !empty($_PATCH['email']) ? $_PATCH['email'] : $klant->email;
                $klant->adres = !empty($_PATCH['adres']) ? $_PATCH['adres'] : $klant->adres;
                $klant->postcode = !empty($_PATCH['postcode']) ? $_PATCH['postcode'] : $klant->postcode;
                $klant->woonplaats = !empty($_PATCH['woonplaats']) ? $_PATCH['woonplaats'] : $klant->woonplaats;
                $klant->save();
                API::printAndExit($klant);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::post("/klant", function(Request $request) {
        $_GET = $request->url->searchParams;
        $_POST = $request->body;

        $amount = $_GET["amount"] ?? 1;
        if($amount < 1) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at least 1");
        else if($amount > 100) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at most 100");

        if($amount === 1) {
            $klant = isset($_GET['random']) ? Klant::generateRandom() : Klant::create();

            if(!empty($_POST['naam'])) $klant->naam = $_POST['naam'];
            if(!empty($_POST['email'])) $klant->email = $_POST['email'];
            if(!empty($_POST['adres'])) $klant->adres = $_POST['adres'];
            if(!empty($_POST['postcode'])) $klant->postcode = $_POST['postcode'];
            if(!empty($_POST['woonplaats'])) $klant->woonplaats = $_POST['woonplaats'];
            $klant->save();

            API::printAndExit($klant);
        } else {
            $klanten = [];
            for($i = 0; $i < $amount; $i++) {
                $klanten[] = Klant::generateRandom();
            }
            API::printAndExit($klanten);
        }
    });