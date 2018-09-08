CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(256) UNIQUE,
    password VARCHAR(256),
    first_name VARCHAR(64),
    last_name VARCHAR(64),
    email VARCHAR(256) UNIQUE,
    created TIMESTAMP WITH TIME ZONE default current_timestamp
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE
);

CREATE TABLE items (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(id) ON DELETE CASCADE,
    fee DECIMAL(8,2) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    pickup_lat DECIMAL(10,8) NOT NULL,
    pickup_long DECIMAL(11,8) NOT NULL,
    return_lat DECIMAL(10,8) NOT NULL,
    return_long DECIMAL(11,8) NOT NULL,
    date_avaiable DATE DEFAULT NULL,
    borrowed BOOLEAN DEFAULT FALSE,
    banner BOOLEAN DEFAULT FALSE,
    created TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp
);

CREATE TABLE item_images (
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    image_link TEXT NOT NULL,
    PRIMARY KEY (item_id, image_link)
);

CREATE TABLE loans (
    owner_id INT REFERENCES users(id) ON DELETE CASCADE,
    borrower_id INT REFERENCES users(id) ON DELETE CASCADE,
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    loan_price DECIMAL(8,2) NOT NULL,
    PRIMARY KEY (owner_id, borrower_id, item_id),
    CONSTRAINT borrower_id CHECK (owner_id != borrower_id)
);

CREATE TABLE bids (
    owner_id INT REFERENCES users(id) ON DELETE CASCADE,
    bidder_id INT REFERENCES users(id) ON DELETE CASCADE,
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    bid_price DECIMAL(8,2) NOT NULL CHECK (bid_price > 0), 
    PRIMARY KEY (owner_id, borrower_id, item_id),
    CONSTRAINT borrower_id CHECK (owner_id != borrower_id)
);

CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(256) UNIQUE,
    password VARCHAR(256)
);