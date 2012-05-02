CREATE TABLE users_preferences (
id serial PRIMARY KEY,
user_id integer NOT NULL,
preference_field varchar(50) NOT NULL,
preference_content varchar(255) NOT NULL,
active bool DEFAULT true,
created timestamp without time zone,
modified timestamp without time zone
);
