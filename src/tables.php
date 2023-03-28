<?php

function create_tables($conn) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS `artikelen` (
            `artId` INT NOT NULL AUTO_INCREMENT,
            `artOmschrijving` VARCHAR(255) NOT NULL,
            `artInkoop` DECIMAL(10,2) NOT NULL,
            `artVerkoop` DECIMAL(10,2) NOT NULL,
            `artVoorraad` INT NOT NULL,
            `artMinVoorraad` INT NOT NULL,
            `artMaxVoorraad` INT NOT NULL,
            `artLocatie` INT NOT NULL,
            `levId` INT NOT NULL,
            PRIMARY KEY (`artId`)
    )",

      "CREATE TABLE IF NOT EXISTS `leveranciers` (
          `levId` INT NOT NULL AUTO_INCREMENT,
          `levNaam` VARCHAR(255) NOT NULL,
          `levContact` VARCHAR(255) NOT NULL,
          `levEmail` VARCHAR(255) NOT NULL,
          `levAdres` VARCHAR(255) NOT NULL,
          `levPostcode` VARCHAR(10) NOT NULL,
          `levPlaats` VARCHAR(255) NOT NULL,
          `levTelefoon` VARCHAR(20) NOT NULL,
          PRIMARY KEY (`levId`)
    )",

      "CREATE TABLE IF NOT EXISTS `inkooporders` (
          `inkoopId` INT NOT NULL AUTO_INCREMENT,
          `artId` INT NOT NULL,
          `levId` INT NOT NULL,
          `inkoopAantal` INT NOT NULL,
          `inkoopDatum` DATE NOT NULL,
          PRIMARY KEY (`inkoopId`)
    )",

     "CREATE TABLE IF NOT EXISTS `klanten` (
         `klantId` INT NOT NULL AUTO_INCREMENT,
         `klantNaam` VARCHAR(255) NOT NULL,
         `klantAdres` VARCHAR(255) NOT NULL,
         `klantPostcode` VARCHAR(10) NOT NULL,
         `klantPlaats` VARCHAR(255) NOT NULL,
         `klantTelefoon` VARCHAR(20) NOT NULL,
         PRIMARY KEY (`klantId`)
    )",
    "CREATE TABLE IF NOT EXISTS `verkooporders` (
       `verkoopId` INT NOT NULL AUTO_INCREMENT,
       `klantId` INT NOT NULL,
       `artId` INT NOT NULL,
       `verkoopAantal` INT NOT NULL,
       `verkoopDatum` DATE NOT NULL,
       PRIMARY KEY (`verkoopId`)
    )",

    "CREATE TABLE IF NOT EXISTS `users` (
       `userId` INT NOT NULL AUTO_INCREMENT,
       `username` VARCHAR(255) NOT NULL,
       `password` VARCHAR(255) NOT NULL,
       `email` VARCHAR(255) NOT NULL,
       `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
       PRIMARY KEY (`userId`)
    )"
];

    foreach ($queries as $query) {
        if ($conn->query($query) === false) {
            throw new Exception("Error creating table: " . $conn->error, 500);
        }
    }
}

function insert_test_data($conn) {

    $test_insert_queries = [

        "INSERT INTO `artikelen` (`artOmschrijving`, `artInkoop`, `artVerkoop`, `artVoorraad`, `artMinVoorraad`, `artMaxVoorraad`, `artLocatie`, `levId`) VALUES ('Test Product', 10.00, 20.00, 50, 10, 100, 1, 1)",

        "INSERT INTO `leveranciers` (`levNaam`, `levContact`, `levEmail`, `levAdres`, `levPostcode`, `levPlaats`, `levTelefoon`) VALUES ('Test Supplier', 'John Doe', 'john.doe@example.com', '123 Main St', '12345', 'New York', '+1 555-123-4567')",

        "INSERT INTO `inkooporders` (`artId`, `levId`, `inkoopAantal`, `inkoopDatum`) VALUES (1, 1, 20, '2023-04-01')",

        "INSERT INTO `klanten` (`klantNaam`, `klantAdres`, `klantPostcode`, `klantPlaats`, `klantTelefoon`) VALUES ('Jane Smith', '456 High St', '67890', 'Los Angeles', '+1 555-987-6543')",

        "INSERT INTO `verkooporders` (`klantId`, `artId`, `verkoopAantal`, `verkoopDatum`) VALUES (1, 1, 5, '2023-04-02')",

        "INSERT INTO `users` (`username`, `password`, `email`, `role`) VALUES ('testuser', 'testpassword', 'testuser@example.com', 'user')"

    ];

    foreach ($test_insert_queries as $index => $query) {
        $result = $conn->query("SELECT COUNT(*) FROM " . get_table_name($index));
        if (!$result) {
            throw new Exception("Error checking table data: " . $conn->error, 500);
        }
        $count = $result->fetch_row()[0];
        if ($count == 0) {
            if ($conn->query($query) === false) {
                throw new Exception("Error inserting test data: " . $conn->error, 500);
            }
        }
    }
}

function get_table_name($index) {
    $table_names = [
        "artikelen",
        "leveranciers",
        "inkooporders",
        "klanten",
        "verkooporders",
        "users"
    ];

    return $table_names[$index];
}
