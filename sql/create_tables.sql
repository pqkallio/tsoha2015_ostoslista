CREATE TABLE Role(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE Account_status(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE Registered_user(
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    user_name VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    joined DATE NOT NULL,
    last_signin DATE,
    available BOOLEAN DEFAULT FALSE,
    key VARCHAR(50) NOT NULL,
    role INTEGER REFERENCES Role(id) ON DELETE SET NULL,
    account_status INTEGER REFERENCES Account_status(id) ON DELETE SET NULL,
    active_list INTEGER REFERENCES List(id) ON DELETE SET NULL
);

CREATE TABLE Role_history(
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE,
    role INTEGER REFERENCES Role(id) ON DELETE CASCADE,
    granted_by VARCHAR(50) NOT NULL DEFAULT 'SYSTEM',
    granted_at TIMESTAMP NOT NULL
);

CREATE TABLE Shared_products(
    id SERIAL PRIMARY KEY,
    user_from INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE,
    user_to INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE
);

CREATE TABLE Unit(
    id SERIAL PRIMARY KEY,
    name_singular varchar(24),
    name_plural varchar(28),
    abbreviation varchar(9)
);

CREATE TABLE Shop(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    location VARCHAR(50)
);

CREATE TABLE Department(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    abbreviation VARCHAR(12)
);

CREATE TABLE Shop_department(
    id SERIAL PRIMARY KEY,
    shop INTEGER REFERENCES Shop(id) ON DELETE CASCADE,
    department INTEGER REFERENCES Department(id) ON DELETE CASCADE,
    ordinal INTEGER,
    user_id INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE
);

CREATE TABLE List(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    owner INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE,
    shop INTEGER REFERENCES Shop(id) ON DELETE SET NULL
);

CREATE TABLE Right_to_list(
    id SERIAL PRIMARY KEY,
    list INTEGER REFERENCES List(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE
);

CREATE TABLE Product (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50),
    department INTEGER REFERENCES Department(id) ON DELETE SET NULL,
    unit INTEGER REFERENCES Unit(id) ON DELETE SET NULL,
    owner INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE
);

CREATE TABLE Product_unit(
    id SERIAL PRIMARY KEY,
    product INTEGER REFERENCES Product(id) ON DELETE CASCADE,
    unit INTEGER REFERENCES Unit(id) ON DELETE CASCADE
);

CREATE TABLE Favorite_product(
    id SERIAL PRIMARY KEY,
    product INTEGER REFERENCES Product(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE
);

CREATE TABLE Purchase(
    id SERIAL PRIMARY KEY,
    list INTEGER REFERENCES List(id) ON DELETE CASCADE,
    product INTEGER REFERENCES Product(id) ON DELETE CASCADE,
    department INTEGER REFERENCES Department(id) ON DELETE SET NULL,
    unit INTEGER REFERENCES Unit(id) ON DELETE SET NULL,
    amount INTEGER CHECK(amount > 0),
    purchase_date TIMESTAMP DEFAULT NULL
);

CREATE TABLE Purchase_history(
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES Registered_user(id) ON DELETE CASCADE,
    product INTEGER REFERENCES Product(id) ON DELETE CASCADE,
    amount INTEGER CHECK(amount > 0),
    unit INTEGER REFERENCES Unit(id) ON DELETE SET NULL,
    purchase_date TIMESTAMP NOT NULL
);