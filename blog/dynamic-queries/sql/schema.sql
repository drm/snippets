CREATE TABLE car (
   car_id BIGINT NOT NULL AUTO_INCREMENT,
   type_id BIGINT NOT NULL,
   title VARCHAR(80) NOT NULL,
   description TEXT,
   fuel ENUM ('diesel', 'gas') NOT NULL DEFAULT 'gas',
   price DECIMAL(16,2),
   build_year TINYINT(4),

   PRIMARY KEY(car_id),
   KEY(type_id),
   KEY(title)
);

CREATE TABLE type(
    type_id BIGINT NOT NULL AUTO_INCREMENT,
    brand_id BIGINT NOT NULL,
    type VARCHAR(255) NOT NULL,
    PRIMARY KEY(type_id),
    KEY(brand_id)
);

CREATE TABLE brand(
    brand_id BIGINT NOT NULL AUTO_INCREMENT,
    brand VARCHAR(255) NOT NULL,
    PRIMARY KEY(brand_id)
);

CREATE TABLE accessory(
    accessory_id BIGINT NOT NULL AUTO_INCREMENT,
    accessory VARCHAR(255) NOT NULL,
    PRIMARY KEY(accessory_id)
);

CREATE TABLE car_accessory(
    car_id BIGINT NOT NULL,
    accessory_id BIGINT NOT NULL,

    PRIMARY KEY(car_id, accessory_id)
);
