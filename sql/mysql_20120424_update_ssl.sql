
ALTER TABLE content_domain ADD COLUMN sslname VARCHAR(255);

ALTER TABLE content_page ADD COLUMN sslreq TINYINT DEFAULT 0;

