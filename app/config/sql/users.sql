CREATE TABLE users (
id serial PRIMARY KEY,
email varchar(100) NOT NULL UNIQUE,
password varchar(255) NOT NULL,
role varchar(20) DEFAULT 'user',
active bool DEFAULT true,
logintime timestamp without time zone,
created timestamp without time zone,
modified timestamp without time zone
);
