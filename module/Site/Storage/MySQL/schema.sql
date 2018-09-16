
CREATE TABLE invoices (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `product` varchar(255) COMMENT 'Product name',
    `status` SMALLINT COMMENT 'Invoice status',
    `amount` FLOAT COMMENT 'Invoice amount',
    `token` varchar(32) COMMENT 'MD5-signature',
    `datetime` DATETIME COMMENT 'Date and time'
);