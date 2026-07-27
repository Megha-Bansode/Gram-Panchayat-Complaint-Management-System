-- ============================================================
-- GPCMS – Complete Database Schema + Seed Data
-- Database: gpcms
-- Handbook: GPCMS Engineering Handbook V3.1
-- Run in: phpMyAdmin OR via MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS gpcms
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gpcms;

-- ── ROLES ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS roles (
    role_id   INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50)  NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (role_name) VALUES
    ('Admin'), ('Officer'), ('Citizen')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- ── USERS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    user_id    INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(150)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    role_id    INT           NOT NULL DEFAULT 3,
    is_active  TINYINT(1)    NOT NULL DEFAULT 1,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── CATEGORIES ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    category_id   INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (category_name) VALUES
    ('Roads & Infrastructure'),
    ('Water Supply'),
    ('Electricity'),
    ('Sanitation & Drainage'),
    ('Public Health'),
    ('Education'),
    ('Agriculture'),
    ('Street Lighting'),
    ('Land & Property'),
    ('Other')
ON DUPLICATE KEY UPDATE category_name = VALUES(category_name);

-- ── COMPLAINTS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id          INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id               INT           NOT NULL,
    category_id           INT           NOT NULL,
    complaint_title       VARCHAR(255)  NOT NULL,
    complaint_description TEXT          NOT NULL,
    village_ward          VARCHAR(150)  NOT NULL,
    status                ENUM('pending','assigned','in_progress','resolved')
                          NOT NULL DEFAULT 'pending',
    assigned_to           INT           NULL DEFAULT NULL,
    submitted_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                          ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(user_id)          ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── COMPLAINT_PHOTOS ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS complaint_photos (
    photo_id     INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT          NOT NULL,
    photo_type   VARCHAR(50)  NOT NULL DEFAULT 'complaint',
    photo_path   VARCHAR(500) NOT NULL,
    uploaded_by  INT          NULL,
    uploaded_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── COMPLAINT_HISTORY ────────────────────────────────────
CREATE TABLE IF NOT EXISTS complaint_history (
    history_id   INT       NOT NULL AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT       NOT NULL,
    status       ENUM('pending','assigned','in_progress','resolved') NOT NULL,
    note         TEXT      NULL,
    updated_by   INT       NULL,
    updated_at   DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── NOTIFICATIONS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT        NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id         INT        NOT NULL,
    complaint_id    INT        NULL,
    message         TEXT       NOT NULL,
    is_read         TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── FEEDBACK ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id  INT        NOT NULL AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT        NOT NULL,
    user_id      INT        NOT NULL,
    rating       TINYINT    NOT NULL CHECK (rating BETWEEN 1 AND 5),
    feedback_text TEXT      NULL,
    submitted_at  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)      REFERENCES users(user_id)           ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── SETTINGS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
    setting_id    INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT         NULL,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── SEED: Demo Citizen Account ────────────────────────────
-- Email: citizen@gpcms.in  |  Password: Citizen@123
-- Hash generated with: password_hash('Citizen@123', PASSWORD_BCRYPT)
INSERT INTO users (full_name, email, password, role_id) VALUES
(
    'Demo Citizen',
    'citizen@gpcms.in',
    '$2y$10$PLACEHOLDER_REPLACE_WITH_PHP_HASH',
    3
)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);
-- NOTE: After importing, run fix_password.php once to set the correct hash.
