DROP TABLE IF EXISTS PersonPhones;

CREATE TABLE PersonPhones (
	name varchar(20),
	phone varchar(20),
	PRIMARY KEY(name)
);

INSERT INTO PersonPhones values('Alice', '123456');
INSERT INTO PersonPhones values('Bob', '987654');
INSERT INTO PersonPhones values('Pelle', '123987');
