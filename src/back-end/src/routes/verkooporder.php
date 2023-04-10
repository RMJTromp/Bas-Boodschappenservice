<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Artikel;
    use Boodschappenservice\objects\Klant;
    use Boodschappenservice\objects\Leverancier;
    use Boodschappenservice\objects\VerkoopOrder;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\ArrayList;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/verkooporder", function() {
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        $limit = min(max($limit, 0), 100);
        $offset = max($offset, 0);

        $verkoopOrders = VerkoopOrder::getAll($limit, $offset);
        $verkoopOrders = new ArrayList($verkoopOrders);

        $meta = [
            'results' => [
                'count' => $verkoopOrders->count(),
                'total' => VerkoopOrder::count(),
                'limit' => $limit,
                'offset' => $offset,
            ]
        ];

        API::printAndExit($verkoopOrders, meta: $meta);
    });

    Route::handle(RegExp::compile("/^\/verkooporder\/(\d+)$/"), function(Request $request, array $matches) {
        $id = intval($matches[1][0]);
        $verkoopOrder = VerkoopOrder::get($id);

        switch($request->method) {
            case "GET":
                API::printAndExit($verkoopOrder);
            case "DELETE":
                $verkoopOrder->delete();
                API::printAndExit([], ResponseCode::OK[0]);
            case "PATCH":
                $_PATCH = $request->body;
                if(!empty($_POST['klant']) && is_numeric($_POST['klant'])) $verkoopOrder->klant = Klant::get(intval($_POST['klant']));
                if(!empty($_PATCH['artikel']) && is_numeric($_PATCH['artikel'])) $verkoopOrder->artikel = Artikel::get(intval($_PATCH['artikel']));
                if(!empty($_PATCH['aantal']) && is_numeric($_PATCH['aantal'])) $verkoopOrder->aantal = intval($_PATCH['aantal']);
                if(!empty($_PATCH['datum']) && is_numeric($_PATCH['datum'])) $verkoopOrder->datum = DateTime::createFromFormat("U", intval($_PATCH['datum']));
                if(!empty($_PATCH['status']) && is_numeric($_PATCH['status'])) $verkoopOrder->status = intval($_PATCH['aantal']);
                $verkoopOrder->save();
                API::printAndExit($verkoopOrder);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::get("/verkoopordere", function(Request $request) {
        $_GET = $request->url->searchParams;
        $_POST = $request->body;

        $amount = $_GET["amount"] ?? 1;
        if($amount < 1) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at least 1");
        else if($amount > 100) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at most 100");

        if($amount === 1) {
            $verkoopOrder = isset($_GET['random']) ? VerkoopOrder::generateRandom() : VerkoopOrder::create();

            if(!empty($_POST['klant']) && is_numeric($_POST['klant'])) $verkoopOrder->klant = Klant::get(intval($_POST['klant']));
            if(!empty($_POST['artikel']) && is_numeric($_POST['artikel'])) $verkoopOrder->artikel = Artikel::get(intval($_POST['artikel']));
            if(!empty($_POST['aantal']) && is_numeric($_POST['aantal'])) $verkoopOrder->aantal = intval($_POST['aantal']);
            if(!empty($_POST['datum']) && is_numeric($_POST['datum'])) $verkoopOrder->datum = DateTime::createFromFormat("U", intval($_POST['datum']));
            if(!empty($_PATCH['status']) && is_numeric($_PATCH['status'])) $verkoopOrder->status = intval($_PATCH['aantal']);
            $verkoopOrder->save();

            API::printAndExit($verkoopOrder);
        } else {
            $verkoopOrders = [];
            for($i = 0; $i < $amount; $i++) {
                $verkoopOrders[] = VerkoopOrder::generateRandom();
            }
            API::printAndExit($verkoopOrders);
        }
    });