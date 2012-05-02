CREATE TABLE logintokens (
id serial PRIMARY KEY,
user_id integer NOT NULL,
token varchar(255) NOT NULL,
created timestamp without time zone,
refreshed timestamp without time zone
);
