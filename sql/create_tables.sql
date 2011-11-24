--
-- Datenbank fuer die LC2008 Anmeldung

CREATE TABLE account (
	accountid	BIGINT UNSIGNED NOT NULL auto_increment,
	username	VARCHAR(20) NOT NULL,
	passwd		VARCHAR(32) NOT NULL,
	email		VARCHAR(255) NOT NULL,
	crdate		DATETIME NOT NULL,
	lugid		BIGINT UNSIGNED,
	PRIMARY KEY pk_account(accountid)
);

CREATE TABLE event_event (
	eventid		BIGINT UNSIGNED NOT NULL auto_increment,
	anfang		DATETIME NOT NULL,
	ende		DATETIME NOT NULL,
	name		VARCHAR(255) NOT NULL,
	beschreibung	TEXT DEFAULT NULL,
	buchanfang	DATETIME NOT NULL,
	buchende	DATETIME NOT NULL,
	quota		INTEGER DEFAULT NULL,
	PRIMARY KEY pk_event (eventid)
);

CREATE TABLE event_anmeldung (
	anmeldungid	BIGINT UNSIGNED NOT NULL auto_increment,
	accountid	BIGINT UNSIGNED NOT NULL,
	lugid		BIGINT UNSIGNED NOT NULL,
	vorname		VARCHAR(30) NOT NULL,
	nachname	VARCHAR(30) NOT NULL,
	strasse		VARCHAR(60) NOT NULL,
	hausnr		VARCHAR(10) NOT NULL,
	plz		VARCHAR(10) NOT NULL,
	land		CHAR(2) NOT NULL DEFAULT 'DE',
	email		VARCHAR(255) NOT NULL,
	gebdat		DATE NOT NULL,
	PRIMARY KEY pk_anmeldung (anmeldungid)
);

CREATE TABLE event_artikel (
	artikelid	BIGINT UNSIGNED NOT NULL auto_increment,
	name		VARCHAR(255) NOT NULL,
	beschreibung	TEXT,
	kaufab		DATETIME DEFAULT NULL,
	kaufbis		DATETIME DEFAULT NULL,
	preis		NUMERIC(5,2) DEFAULT NULL,
	pic		VARCHAR(255) DEFAULT NULL,	-- Bild
	PRIMARY KEY pk_artikel(artikelid)
);

CREATE TABLE event_account_artikel (
	accountid	BIGINT UNSIGNED NOT NULL,
	artikelid	BIGINT UNSIGNED NOT NULL,
	anzahl		INTEGER NOT NULL,
	crdate		DATETIME,
	PRIMARY KEY pk_account_artikel(accountid,artikelid,anzahl)
);

CREATE TABLE event_lug (
	lugid		BIGINT UNSIGNED NOT NULL auto_increment,
	name		VARCHAR(255) NOT NULL,
	abk		VARCHAR(10) DEFAULT NULL,
	url		VARCHAR(255) DEFAULT NULL,
	crdate		DATETIME NOT NULL,
	PRIMARY KEY pk_lug (lugid)
);

CREATE TABLE event_anmeldung_event (
	anmeldungid	BIGINT UNSIGNED NOT NULL,
	eventid		BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY pk_anmeldung_event(anmeldungid,eventid)
);

CREATE TABLE event_zahlung (
	zahlungid	BIGINT UNSIGNED NOT NULL auto_increment,
	accountid	BIGINT UNSIGNED NOT NULL,
	crdate		DATETIME NOT NULL,
	valutadate	DATETIME NOT NULL,
	valuta		NUMERIC(5,2),
	txt		TEXT DEFAULT NULL,
	PRIMARY KEY pk_zahlung(zahlungid)
);

CREATE TABLE news_cat (
	catid	BIGINT UNSIGNED NOT NULL auto_increment,
	name	VARCHAR(50) NOT NULL,
	pic	VARCHAR(255) DEFAULT NULL,
	PRIMARY KEY pk_news_cat(catid)
);

CREATE TABLE news_eintrag (
	eintragid	BIGINT UNSIGNED NOT NULL auto_increment,
	title		VARCHAR(80) NOT NULL,
	catid		BIGINT UNSIGNED NOT NULL,
	short		TEXT NOT NULL,
	txt		TEXT NOT NULL,
	crdate		DATETIME NOT NULL,
	author		VARCHAR(50) NOT NULL,
	PRIMARY KEY pk_news_eintrag(eintragid)
);

CREATE TABLE sponsoren (
  sponsorenid BIGINT UNSIGNED NOT NULL auto_increment,
  name VARCHAR(255) NOT NULL,
  url VARCHAR(255),
  img VARCHAR(255),
  crdate DATETIME NOT NULL,
  PRIMARY KEY pk_sponsoren(sponsorenid)
);

