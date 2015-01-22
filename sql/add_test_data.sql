INSERT INTO Role (name) VALUES ('admin');
INSERT INTO Role (name) VALUES ('user');

INSERT INTO Account_status (name) VALUES ('active');
INSERT INTO Account_status (name) VALUES ('passive');
INSERT INTO Account_status (name) VALUES ('closed');

INSERT INTO Registered_user (first_name, last_name, user_name, email, joined,
    key, role, account_status) 
    VALUES ('Keke', 'Ruusperi', 'keke', 'keke@formula.com', NOW(), 'kekru123',
            2, 1);
INSERT INTO Registered_user (first_name, last_name, user_name, email, joined,
    last_signin, available, key, role, account_status) 
    VALUES ('Chuck', 'Norris', 'kickass', 'ranger@texas.us', '1972-12-31', 
            NOW(), true, 'ranger987', 1, 2);
INSERT INTO Registered_user (first_name, last_name, user_name, email, joined,
    last_signin, key, role, account_status) 
    VALUES ('Bad', 'Boy', 'badboy', 'badboy@rebelyell.org', '1984-10-15', 
            '1985-01-12', 'badboybadboybadbadboy', 2, 3);

INSERT INTO Role_history (user_id, role, granted_at) 
    VALUES (2, 1, '1972-12-31');
INSERT INTO Role_history (user_id, role, granted_at) 
    VALUES (1, 2, NOW());
INSERT INTO Role_history (user_id, role, granted_at) 
    VALUES (3, 2, '1984-10-15');
INSERT INTO Role_history (user_id, role, granted_by, granted_at) 
    VALUES (3, 1, 'kickass', '1984-11-14');
INSERT INTO Role_history (user_id, role, granted_by, granted_at) 
    VALUES (3, 2, 'kickass', '1985-01-10');

INSERT INTO Shared_products (user_from, user_to) VALUES (2, 3);
INSERT INTO Shared_products (user_from, user_to) VALUES (3, 2);

INSERT INTO Unit (name_singular, name_plural, abbreviation) 
    VALUES ('kappale', 'kappaletta', 'kpl');
INSERT INTO Unit (name_singular, name_plural, abbreviation) 
    VALUES ('litra', 'litraa', 'l');
INSERT INTO Unit (name_singular, name_plural, abbreviation) 
    VALUES ('gramma', 'grammaa', 'g');
INSERT INTO Unit (name_singular, name_plural, abbreviation) 
    VALUES ('tölkki', 'tölkkiä', 'tlk');
INSERT INTO Unit (name_singular, name_plural, abbreviation) 
    VALUES ('pussi', 'pussia', 'ps');
INSERT INTO Unit (name_singular, name_plural, abbreviation) 
    VALUES ('paketti', 'pakettia', 'pkt');

INSERT INTO Shop (name, location) VALUES ('Prisma', 'Riihimäki');
INSERT INTO Shop (name, location) VALUES ('Ikea', 'Vantaa');
INSERT INTO Shop (name, location) VALUES ('City-Market', 'Jumbo');

INSERT INTO Department (name, abbreviation) 
    VALUES ('hedelmät ja vihannekset', 'hevi');
INSERT INTO Department (name, abbreviation) 
    VALUES ('maitotuotteet', 'maidot');
INSERT INTO Department (name) VALUES ('liha');
INSERT INTO Department (name) VALUES ('leipä');
INSERT INTO Department (name) VALUES ('säilykkeet');

INSERT INTO Shop_department (shop, department, ordinal, user_id) 
    VALUES (1, 1, 1, 2);
INSERT INTO Shop_department (shop, department, ordinal, user_id) 
    VALUES (1, 3, 2, 2);
INSERT INTO Shop_department (shop, department, ordinal, user_id) 
    VALUES (1, 2, 3, 2);
INSERT INTO Shop_department (shop, department, ordinal, user_id) 
    VALUES (1, 5, 4, 2);
INSERT INTO Shop_department (shop, department, ordinal, user_id) 
    VALUES (1, 4, 5, 2);

INSERT INTO List (name, owner, shop) VALUES ('Ruokakauppa', 1, 1);
INSERT INTO List (name, owner, shop, active) VALUES ('Ikea', 1, 2, false);
INSERT INTO List (name, owner, shop) VALUES ('Safkat', 2, 3);

INSERT INTO Right_to_list(list, user_id) VALUES (1, 2);
INSERT INTO Right_to_list(list, user_id) VALUES (3, 1);

INSERT INTO Product (name, department, unit, owner) 
    VALUES ('kurkku', 1, 1, 2);
INSERT INTO Product (name, department, unit, owner) 
    VALUES ('maito', 2, 2, 2);
INSERT INTO Product (name, department, unit, owner) 
    VALUES ('jauheliha', 3, 3, 1);

INSERT INTO Product_unit (product, unit) VALUES (2, 4);
INSERT INTO Product_unit (product, unit) VALUES (3, 6);

INSERT INTO Favorite_product (product, user_id) VALUES (1, 1);
INSERT INTO Favorite_product (product, user_id) VALUES (2, 1);
INSERT INTO Favorite_product (product, user_id) VALUES (1, 2);
INSERT INTO Favorite_product (product, user_id) VALUES (3, 2);

INSERT INTO Purchase (list, product, amount) VALUES (1, 1, 2);
INSERT INTO Purchase (list, product, amount) VALUES (1, 2, 4);
INSERT INTO Purchase (list, product, amount) VALUES (1, 3, 700);
INSERT INTO Purchase (list, product, amount) VALUES (3, 1, 1);
INSERT INTO Purchase (list, product, unit, amount) VALUES (3, 2, 4, 2);
INSERT INTO Purchase (list, product, unit, amount) VALUES (3, 3, 6, 2);

INSERT INTO Purchase_history (user_id, product, amount, unit, purchase_date) 
    VALUES (2, 1, 4, 1, '1998-12-04');
INSERT INTO Purchase_history (user_id, product, amount, unit, purchase_date) 
    VALUES (1, 2, 5, 2, NOW());
INSERT INTO Purchase_history (user_id, product, amount, unit, purchase_date) 
    VALUES (2, 3, 800, 3, '1998-12-04');