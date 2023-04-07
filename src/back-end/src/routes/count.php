<?php

    \Boodschappenservice\core\Route::get("/count", fn() => \Boodschappenservice\utilities\API::printAndExit([
        "leveranciers" => \Boodschappenservice\objects\Leverancier::count(),
        "artikelen" => \Boodschappenservice\objects\Artikel::count(),
        "klanten" => \Boodschappenservice\objects\Klant::count(),
    ]));