<?php

    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\Artikel;
    use Boodschappenservice\objects\InkoopOrder;
    use Boodschappenservice\objects\Klant;
    use Boodschappenservice\objects\Leverancier;
    use Boodschappenservice\objects\VerkoopOrder;
    use Boodschappenservice\utilities\API;

    Route::get("/count", fn() => API::printAndExit([
        "leveranciers" => Leverancier::count(),
        "artikelen" => Artikel::count(),
        "klanten" => Klant::count(),
        "inkooporders" => InkoopOrder::count(),
        "verkooporders" => VerkoopOrder::count()
    ]));