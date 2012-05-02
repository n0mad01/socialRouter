CREATE TABLE socialaccounts (
id serial PRIMARY KEY,
user_id integer NOT NULL,
service varchar(100) NOT NULL,
user_service_id integer NULL,
username varchar(100) NULL,
image varchar(255) NULL,
token varchar(255) NULL,
token_secret varchar(255) NULL,
active bool DEFAULT true,
created timestamp without time zone,
modified timestamp without time zone
);
