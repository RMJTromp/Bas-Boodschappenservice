CREATE TABLE IF NOT EXISTS `leveranciers` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `naam` VARCHAR(15) NOT NULL,
    `contact` VARCHAR(20) NOT NULL,
    `email` VARCHAR(30) NOT NULL,
    `adres` VARCHAR(30) NOT NULL,
    `postcode` VARCHAR(6) NOT NULL,
    `woonplaats` VARCHAR(25) NOT NULL,
    PRIMARY KEY (`id`)
)