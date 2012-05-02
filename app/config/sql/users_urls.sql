CREATE TABLE users_urls (
id serial PRIMARY KEY,
user_id integer NOT NULL,
url_id integer NOT NULL,
created timestamp without time zone,
modified timestamp without time zone
);
