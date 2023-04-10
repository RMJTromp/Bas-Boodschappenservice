<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Artikel;
    use Boodschappenservice\objects\InkoopOrder;
    use Boodschappenservice\objects\Leverancier;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\ArrayList;
    use Boodschappenservice\utilities\RegExp;
    use Boodschappenservice\utilities\ResponseCode;

    Route::get("/inkooporders", function() {
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        $limit = min(max($limit, 0), 100);
        $offset = max($offset, 0);

        $inkoopOrders = InkoopOrder::getAll($limit, $offset);
        $inkoopOrders = new ArrayList($inkoopOrders);

        $meta = [
            'results' => [
                'count' => $inkoopOrders->count(),
                'total' => InkoopOrder::count(),
                'limit' => $limit,
                'offset' => $offset,
            ]
        ];

        API::printAndExit($inkoopOrders, meta: $meta);
    });

    Route::handle(RegExp::compile("/^\/inkooporder\/(\d+)$/"), function(Request $request, array $matches) {
        $id = intval($matches[1][0]);
        $inkoopOrder = InkoopOrder::get($id);

        switch($request->method) {
            case "GET":
                API::printAndExit($inkoopOrder);
            case "DELETE":
                $inkoopOrder->delete();
                API::printAndExit([], ResponseCode::OK[0]);
            case "PATCH":
                $_PATCH = $request->body;
                if(!empty($_PATCH['leverancier']) && is_numeric($_PATCH['leverancier'])) $inkoopOrder->leverancier = Leverancier::get(intval($_PATCH['leverancier']));
                if(!empty($_PATCH['artikel']) && is_numeric($_PATCH['artikel'])) $inkoopOrder->artikel = Artikel::get(intval($_PATCH['artikel']));
                if(!empty($_PATCH['aantal']) && is_numeric($_PATCH['aantal'])) $inkoopOrder->aantal = intval($_PATCH['aantal']);
                if(!empty($_PATCH['datum']) && is_numeric($_PATCH['datum'])) $inkoopOrder->datum = DateTime::createFromFormat("U", intval($_PATCH['datum']));
                if(!empty($_PATCH['geleverd'])) {
                    $_PATCH['geleverd'] = strtolower(trim($_PATCH['geleverd']));
                    if($_PATCH['geleverd'] === "true" || $_PATCH['geleverd'] === "false")
                        $inkoopOrder->geleverd = $_PATCH['geleverd'] === "true";
                }
                $inkoopOrder->save();
                API::printAndExit($inkoopOrder);
            default:
                API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
        }
    });

    Route::get("/inkooporder", function(Request $request) {
        $_GET = $request->url->searchParams;
        $_POST = $request->body;

        $amount = $_GET["amount"] ?? 1;
        if($amount < 1) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at least 1");
        else if($amount > 100) API::printAndExit([], ResponseCode::BAD_REQUEST[0], "Amount must be at most 100");

        if($amount === 1) {
            $inkoopOrder = isset($_GET['random']) ? InkoopOrder::generateRandom() : InkoopOrder::create();

            if(!empty($_POST['leverancier']) && is_numeric($_POST['leverancier'])) $inkoopOrder->leverancier = Leverancier::get(intval($_POST['leverancier']));
            if(!empty($_POST['artikel']) && is_numeric($_POST['artikel'])) $inkoopOrder->artikel = Artikel::get(intval($_POST['artikel']));
            if(!empty($_POST['aantal']) && is_numeric($_POST['aantal'])) $inkoopOrder->aantal = intval($_POST['aantal']);
            if(!empty($_POST['datum']) && is_numeric($_POST['datum'])) $inkoopOrder->datum = DateTime::createFromFormat("U", intval($_POST['datum']));
            if(!empty($_POST['geleverd'])) {
                $_POST['geleverd'] = strtolower(trim($_POST['geleverd']));
                if($_POST['geleverd'] === "true" || $_POST['geleverd'] === "false")
                    $inkoopOrder->geleverd = $_POST['geleverd'] === "true";
            }
            $inkoopOrder->save();

            API::printAndExit($inkoopOrder);
        } else {
            $inkoopOrders = [];
            for($i = 0; $i < $amount; $i++) {
                $inkoopOrders[] = InkoopOrder::generateRandom();
            }
            API::printAndExit($inkoopOrders);
        }
    });