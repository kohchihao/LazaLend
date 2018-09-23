SET search_path TO lazalend;

INSERT INTO items (user_id, category_id, fee, name, pickup_lat, pickup_long, return_lat, return_long)
    VALUES (1, 1, 5, 'The Black Swan: The Impact of the Highly Improbable', 1.2966, 103.7764, 1.2966, 103.7764);
INSERT INTO items (user_id, category_id, fee, name, description, pickup_lat, pickup_long, return_lat, return_long)
    VALUES (1, 2, 25, 'iPhone X','coolest iPhone X in town to be loaned', 1.2966, 103.7764, 1.2966, 103.7764);
INSERT INTO items (user_id, category_id, fee, name, description, pickup_lat, pickup_long, return_lat, return_long)
    VALUES (1, 3, 70, 'Canon 7D','brand new Cannon 7D', 1.2966, 103.7764, 1.2966, 103.7764);