CREATE TABLE IF NOT EXISTS `ads`.`sets`
(
    `id` BIGINT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `ads`.`campaigns`
(
    `id` BIGINT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `ads`.`ads`
(
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `expenditures` DOUBLE NOT NULL,
    `ad_id` BIGINT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `campaign` BIGINT NOT NULL,
    `set` BIGINT NOT NULL,
    `views` BIGINT NOT NULL,
    `clicks` BIGINT NOT NULL,

    PRIMARY KEY (`id`),
    
    FOREIGN KEY (`campaign`)
        REFERENCES `ads`.`campaigns`(`id`),
    
    FOREIGN KEY (`set`)
        REFERENCES `ads`.`sets`(`id`)
);
