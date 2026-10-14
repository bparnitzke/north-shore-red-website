ALTER TABLE contact_submission_receipts
  ADD COLUMN form_type VARCHAR(64) NOT NULL DEFAULT 'contact' AFTER source_url,
  ADD COLUMN submission_metadata_json JSON NULL AFTER interests_json,
  ADD COLUMN email_consent TINYINT(1) NOT NULL DEFAULT 0 AFTER submission_metadata_json;

UPDATE contact_submission_receipts
SET submission_metadata_json = JSON_OBJECT('form_type', form_type)
WHERE submission_metadata_json IS NULL;

ALTER TABLE contact_submission_receipts
  MODIFY COLUMN submission_metadata_json JSON NOT NULL;

CREATE TABLE email_consent_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  contact_submission_id CHAR(36) NULL,
  event_type VARCHAR(32) NOT NULL,
  event_at DATETIME(6) NOT NULL,
  email VARCHAR(320) NULL,
  source_url VARCHAR(2048) NOT NULL,
  program_id VARCHAR(128) NOT NULL,
  consent_language_version VARCHAR(64) NOT NULL,
  consent_language_text TEXT NOT NULL,
  metadata_json JSON NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_email_events_email_program_time (email, program_id, event_at),
  INDEX idx_email_events_submission (contact_submission_id),
  CONSTRAINT fk_email_events_submission
    FOREIGN KEY (contact_submission_id) REFERENCES contact_submission_receipts (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
