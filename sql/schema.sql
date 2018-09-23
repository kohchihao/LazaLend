SET search_path TO lazalend;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(256) UNIQUE NOT NULL,
    password VARCHAR(256) NOT NULL,
    first_name VARCHAR(64) NOT NULL,
    last_name VARCHAR(64) NOT NULL,
    email VARCHAR(256) UNIQUE ,
    profile_image_url TEXT DEFAULT '',
    created TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp,
    last_updated TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(256) UNIQUE NOT NULL,
    image_url TEXT NOT NULL
);

CREATE TABLE items (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(id) ON DELETE CASCADE,
    fee DECIMAL(8,2) NOT NULL,
    name VARCHAR(256) NOT NULL,
    description TEXT DEFAULT '',
    pickup_lat DECIMAL(10,8) NOT NULL,
    pickup_long DECIMAL(11,8) NOT NULL,
    return_lat DECIMAL(10,8) NOT NULL,
    return_long DECIMAL(11,8) NOT NULL,
    date_available DATE DEFAULT current_timestamp,
    borrowed BOOLEAN DEFAULT FALSE,
    promoted BOOLEAN DEFAULT FALSE,
    created TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp,
    last_updated TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp
);

CREATE TABLE item_images (
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    image_link TEXT NOT NULL,
    PRIMARY KEY (item_id, image_link)
);

CREATE TABLE bids (
    id SERIAL PRIMARY KEY,
    owner_id INT REFERENCES users(id) ON DELETE CASCADE,
    bidder_id INT REFERENCES users(id) ON DELETE CASCADE,
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    bid_price DECIMAL(8,2) NOT NULL CHECK (bid_price >= 0),
    duration_of_loan INT NOT NULL CHECK (duration_of_loan > 0 AND duration_of_loan < 30),
    date_of_loan DATE NOT NULL,
    created TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp,
    last_updated TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp, 
    UNIQUE (owner_id, bidder_id, item_id),
    CONSTRAINT bidder_id CHECK (owner_id != bidder_id)
);

CREATE TABLE loans (
    owner_id INT REFERENCES users(id) ON DELETE CASCADE,
    borrower_id INT REFERENCES users(id) ON DELETE CASCADE,
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    bid_id INT PRIMARY KEY REFERENCES bids(id) ON DELETE CASCADE,
    created TIMESTAMP WITH TIME ZONE DEFAULT current_timestamp,
    CONSTRAINT borrower_id CHECK (owner_id != borrower_id)
);

CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(256) UNIQUE,
    password VARCHAR(256)
);