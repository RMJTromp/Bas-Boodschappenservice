# CREATE TABLE IF NOT EXISTS `leveranciers` (
#     `id` INT NOT NULL AUTO_INCREMENT,
#     `naam` VARCHAR(15) NOT NULL,
#     `contact` VARCHAR(20) NOT NULL,
#     `email` VARCHAR(30) NOT NULL,
#     `adres` VARCHAR(30) NOT NULL,
#     `postcode` VARCHAR(6) NOT NULL,
#     `woonplaats` VARCHAR(25) NOT NULL,
#     PRIMARY KEY (`id`)
# );

CREATE TABLE IF NOT EXISTS `artikelen` (
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
);

CREATE TABLE IF NOT EXISTS `leveranciers` (
      `levId` INT NOT NULL AUTO_INCREMENT,
      `levNaam` VARCHAR(255) NOT NULL,
      `levContact` VARCHAR(255) NOT NULL,
      `levEmail` VARCHAR(255) NOT NULL,
      `levAdres` VARCHAR(255) NOT NULL,
      `levPostcode` VARCHAR(10) NOT NULL,
      `levPlaats` VARCHAR(255) NOT NULL,
      `levTelefoon` VARCHAR(20) NOT NULL,
      PRIMARY KEY (`levId`)
);

CREATE TABLE IF NOT EXISTS `inkooporders` (
      `inkoopId` INT NOT NULL AUTO_INCREMENT,
      `artId` INT NOT NULL,
      `levId` INT NOT NULL,
      `inkoopAantal` INT NOT NULL,
      `inkoopDatum` DATE NOT NULL,
      PRIMARY KEY (`inkoopId`)
);

CREATE TABLE IF NOT EXISTS `klanten` (
     `klantId` INT NOT NULL AUTO_INCREMENT,
     `klantNaam` VARCHAR(255) NOT NULL,
     `klantAdres` VARCHAR(255) NOT NULL,
     `klantPostcode` VARCHAR(10) NOT NULL,
     `klantPlaats` VARCHAR(255) NOT NULL,
     `klantTelefoon` VARCHAR(20) NOT NULL,
     PRIMARY KEY (`klantId`)
);

CREATE TABLE IF NOT EXISTS `verkooporders` (
   `verkoopId` INT NOT NULL AUTO_INCREMENT,
   `klantId` INT NOT NULL,
   `artId` INT NOT NULL,
   `verkoopAantal` INT NOT NULL,
   `verkoopDatum` DATE NOT NULL,
   PRIMARY KEY (`verkoopId`)
);

CREATE TABLE IF NOT EXISTS `users` (
   `userId` INT NOT NULL AUTO_INCREMENT,
   `username` VARCHAR(255) NOT NULL,
   `password` VARCHAR(255) NOT NULL,
   `email` VARCHAR(255) NOT NULL,
   `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
   PRIMARY KEY (`userId`)
);