INSERT INTO brand(brand_id, brand) 
VALUES
    (1, 'Peugeot'),
    (2, 'Ford'),
    (3, 'Aston Martin')
;

INSERT INTO type(type_id, brand_id, type)
VALUES
    (1, 1, '206'),
    (2, 1, '207'),
    (3, 1, '307'),
    (4, 2, 'Mondeo'),
    (5, 2, 'Transit'),
    (6, 3, 'DB9'),
    (7, 3, 'Vanquish')
;


INSERT INTO accessory(accessory_id, accessory)
VALUES
    (1, 'Electric windows'),
    (2, 'Remote control'),
    (3, 'Champagne compartment'),
    (4, 'Inflated tyres'),
    (5, 'Airbags'),
    (6, 'Airco'),
    (7, 'Rocket Launcher'),
    (8, 'Bucket seats')
;

INSERT INTO car(car_id, type_id, title, description, fuel, price, build_year)
VALUES
    (1, 4, 'Handy business van', 'This excellent van is particularly suitable for hit-and-run bank robberies', 'diesel', 12000, 2001),
    (2, 7, 'Former secret agent''s wheels', 'Whenever you feel like solving crimes, this 007 car really stands out in covering your needs', 'gas', 230000, 2004),
    (3, 2, 'Old rally car, really solid build', 'I used to own one just like these!', 'gas', 1200, 1999)
;


INSERT INTO car_accessory(car_id, accessory_id)
VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (2, 2),
    (2, 3),
    (2, 5),
    (2, 6),
    (2, 7),
    (2, 8),
    (3, 4),
    (3, 8)
;
