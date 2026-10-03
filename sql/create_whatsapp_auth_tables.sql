-- ============================================================================
-- WhatsApp OTP Authentication Tables for KPSTA Mobile App
-- Run this on the live database to enable WhatsApp OTP login.
-- ============================================================================

-- Table: api_otps
-- Stores OTP hashes, attempts and expiry for WhatsApp-based login
CREATE TABLE IF NOT EXISTS `api_otps` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone`               VARCHAR(20)     NOT NULL,
  `otp_hash`            VARCHAR(64)     NOT NULL COMMENT 'SHA-256 hash of the 6-digit OTP',
  `attempts`            TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at`          DATETIME        NOT NULL,
  `resend_available_at` DATETIME        NOT NULL,
  `verified_at`         DATETIME        DEFAULT NULL,
  `ip_address`          VARCHAR(45)     DEFAULT NULL,
  `created_at`          DATETIME        NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_otps_phone_expires` (`phone`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: api_tokens
-- Stores access/refresh token pairs for authenticated mobile sessions
CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id`                        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`                   INT UNSIGNED    NOT NULL,
  `access_token`              VARCHAR(128)    NOT NULL,
  `refresh_token`             VARCHAR(128)    NOT NULL,
  `device_id`                 VARCHAR(255)    DEFAULT NULL,
  `device_type`               VARCHAR(30)     NOT NULL DEFAULT 'mobile',
  `access_token_expires_at`   DATETIME        NOT NULL,
  `refresh_token_expires_at`  DATETIME        NOT NULL,
  `revoked_at`                DATETIME        DEFAULT NULL,
  `created_at`                DATETIME        NOT NULL,
  `updated_at`                DATETIME        NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_tokens_user`    (`user_id`),
  INDEX `idx_tokens_access`  (`access_token`),
  INDEX `idx_tokens_refresh` (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
