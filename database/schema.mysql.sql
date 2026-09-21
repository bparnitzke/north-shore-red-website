CREATE TABLE contact_submission_receipts (
  id CHAR(36) NOT NULL,
  submitted_at DATETIME(6) NOT NULL,
  source_url VARCHAR(2048) NOT NULL,
  interests_json JSON NOT NULL,
  sms_consent TINYINT(1) NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_contact_receipts_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sms_consent_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  contact_submission_id CHAR(36) NULL,
  event_type VARCHAR(32) NOT NULL,
  event_at DATETIME(6) NOT NULL,
  phone VARCHAR(32) NULL,
  source_url VARCHAR(2048) NOT NULL,
  program_id VARCHAR(128) NOT NULL,
  consent_language_version VARCHAR(64) NOT NULL,
  consent_language_text TEXT NOT NULL,
  metadata_json JSON NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_sms_events_phone_program_time (phone, program_id, event_at),
  INDEX idx_sms_events_submission (contact_submission_id),
  CONSTRAINT fk_sms_events_submission
    FOREIGN KEY (contact_submission_id) REFERENCES contact_submission_receipts (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Treat sms_consent_events as append-only. A future provider webhook should
-- append opt_out, help, and re_opt_in events rather than changing old rows.
