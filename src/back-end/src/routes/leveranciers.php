<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Leverancier;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\ArrayList;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/leveranciers", function() {
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        $limit = min(max($limit, 1), 100);
        $offset = max($offset, 0);

        $search = strtolower($_GET['search'] ?? "");
        $treshold = floatval($_GET['treshold'] ?? 0.5);

        $leveranciers = new ArrayList(Leverancier::getAll());

        if(!empty($search)) {
            $searchLen = strlen($search);
            $leveranciers = $leveranciers
                ->map(function(Leverancier $leverancier) use ($search, $searchLen) {
                    $naam = strtolower($leverancier->naam);
                    return [
                        'object' => $leverancier,
                        'match' => 1 - (levenshtein($naam, $search) / max(strlen($naam), $searchLen))
                    ];
                })
                ->sort(fn(array $a, array $b) => $b['match'] <=> $a['match'])
                ->filter(fn(array $a) => $a['match'] > $treshold)
                ->map(fn(array $a) => $a['object']);
        }

        API::printAndExit($leveranciers->slice($offset, $limit));
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

        $amount = $_GET["amount"] ?? 1;
        if($amount < 1) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at least 1");
        else if($amount > 100) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at most 100");

        if($amount === 1) {
            $leverancier = isset($_GET['random']) ? Leverancier::generateRandom() : Leverancier::create();

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