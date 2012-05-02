CREATE TABLE users_shorteners (
id serial PRIMARY KEY,
user_id integer NOT NULL,
service varchar(20) NOT NULL,
username varchar(30) NULL,
apikey varchar(255) NULL,
token varchar(255) NULL,
token_secret varchar(255) NULL,
active bool DEFAULT TRUE,
created timestamp without time zone,
modified timestamp without time zone
);
