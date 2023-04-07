<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Artikel;
    use Boodschappenservice\objects\Leverancier;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\ArrayList;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/artikels", function() {
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        $limit = min(max($limit, 1), 100);
        $offset = max($offset, 0);

        $search = strtolower($_GET['search'] ?? "");
        $treshold = floatval($_GET['treshold'] ?? 0.5);

        $artikels = new ArrayList(Artikel::getAll());

        if(!empty($search)) {
            $searchLen = strlen($search);
            $artikels = $artikels
                ->map(function(Artikel $artikel) use ($search, $searchLen) {
                    $naam = strtolower($artikel->omschrijving);
                    $match = 1 - (levenshtein($naam, $search) / max(strlen($naam), $searchLen));
                    if(str_contains($naam, $search)) $match = ($match + 1) / 2;
                    return [
                        'object' => $artikel,
                        'match' => $match
                    ];
                })
                ->sort(fn(array $a, array $b) => $b['match'] <=> $a['match'])
                ->filter(fn(array $a) => $a['match'] > $treshold)
                ->map(fn(array $a) => $a['object']);
        }

        API::printAndExit($artikels->slice($offset, $limit));
    });

    Route::handle(RegExp::compile("/^\/artikel\/(\d+)$/"), function(Request $request, array $matches) {
        $id = intval($matches[1][0]);
        $artikel = Artikel::get($id);

        switch($request->method) {
            case "GET":
                API::printAndExit($artikel);
            case "DELETE":
                $artikel->delete();
                API::printAndExit([], ResponseCode::OK[0]);
            case "PATCH":
                $_PATCH = $request->body;

                $artikel->omschrijving = !empty($_PATCH['omschrijving']) ? $_PATCH['omschrijving'] : $artikel->omschrijving;
                $artikel->inkoopPrijs = !empty($_PATCH['inkoopPrijs']) && is_numeric($_PATCH['inkoopPrijs'])
                    ? floor(floatval($_PATCH['inkoopPrijs']) * 100) / 100
                    : $artikel->inkoopPrijs;
                $artikel->verkoopPrijs = !empty($_PATCH['verkoopPrijs']) && is_numeric($_PATCH['verkoopPrijs'])
                    ? floor(floatval($_PATCH['verkoopPrijs']) * 100) / 100
                    : $artikel->verkoopPrijs;
                $artikel->voorraad = !empty($_PATCH['voorraad']) && is_numeric($_PATCH['voorraad'])
                    ? intval($_PATCH['voorraad'])
                    : $artikel->voorraad;
                $artikel->minVoorraad = !empty($_PATCH['minVoorraad']) && is_numeric($_PATCH['minVoorraad'])
                    ? intval($_PATCH['minVoorraad'])
                    : $artikel->minVoorraad;
                $artikel->maxVoorraad = !empty($_PATCH['maxVoorraad']) && is_numeric($_PATCH['maxVoorraad'])
                    ? intval($_PATCH['maxVoorraad'])
                    : $artikel->maxVoorraad;
                $artikel->locatie = !empty($_PATCH['locatie']) && is_numeric($_PATCH['locatie'])
                    ? intval($_PATCH['locatie'])
                    : $artikel->locatie;
                $artikel->leverancier = !empty($_PATCH['leverancier']) && is_numeric($_PATCH['leverancier'])
                    ? Leverancier::get(intval($_PATCH['leverancier']))
                    : $artikel->leverancier;
                $artikel->save();

                API::printAndExit($artikel);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::post("/artikel", function(Request $request) {
        $_GET = $request->url->searchParams;
        $_POST = $request->body;

        $amount = $_GET["amount"] ?? 1;
        if($amount < 1) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at least 1");
        else if($amount > 100) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at most 100");

        if($amount === 1) {
            $artikel = isset($_GET['random']) ? Artikel::generateRandom() : Artikel::create();

            if(!empty($_POST['omschrijving'])) $artikel->omschrijving = $_POST['omschrijving'];
            if(!empty($_POST['inkoopPrijs']) && is_numeric($_POST['inkoopPrijs'])) $artikel->inkoopPrijs = floor(floatval($_POST['inkoopPrijs']) * 100) / 100;
            else $artikel->inkoopPrijs = null;
            if(!empty($_POST['verkoopPrijs']) && is_numeric($_POST['verkoopPrijs'])) $artikel->verkoopPrijs = floor(floatval($_POST['verkoopPrijs']) * 100) / 100;
            else $artikel->verkoopPrijs = null;
            if(!empty($_POST['voorraad']) && is_numeric($_POST['voorraad'])) $artikel->voorraad = intval($_POST['voorraad']);
            if(!empty($_POST['minVoorraad']) && is_numeric($_POST['minVoorraad'])) $artikel->minVoorraad = intval($_POST['minVoorraad']);
            if(!empty($_POST['maxVoorraad']) && is_numeric($_POST['maxVoorraad'])) $artikel->maxVoorraad = intval($_POST['maxVoorraad']);
            if(!empty($_POST['locatie']) && is_numeric($_POST['locatie'])) $artikel->locatie = intval($_POST['locatie']);
            else $artikel->locatie = null;
            if(!empty($_POST['leverancier']) && is_numeric($_POST['leverancier'])) $artikel->leverancier = Leverancier::get(intval($_POST['leverancier']));
            $artikel->save();

            API::printAndExit($artikel);
        } else {
            $artikels = [];
            for($i = 0; $i < $amount; $i++) {
                $artikels[] = Artikel::generateRandom();
            }
            API::printAndExit($artikels);
        }
    });