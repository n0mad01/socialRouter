CREATE TABLE urls (
id serial PRIMARY KEY,
shortcode varchar(10) NOT NULL UNIQUE,
url TEXT NOT NULL UNIQUE,
active bool DEFAULT true,
created timestamp without time zone,
modified timestamp without time zone
);
