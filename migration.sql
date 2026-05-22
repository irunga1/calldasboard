-- ============================================================
--  CallDashboard — Migración inicial
--  Ejecutar: mysql -u root -p'Abc1234!' < migration.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS calldashboard
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE calldashboard;

CREATE TABLE IF NOT EXISTS roles (
    id        INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50)     NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(80)     NOT NULL UNIQUE,
    password_hash VARCHAR(255)    NOT NULL,
    role_id       INT UNSIGNED    NOT NULL DEFAULT 1,
    created_at    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Roles base
INSERT INTO roles (role_name) VALUES ('admin'), ('operator')
ON DUPLICATE KEY UPDATE role_name = role_name;

-- Usuario administrador por defecto  (contraseña: admin123)
INSERT INTO users (username, password_hash, role_id)
VALUES ('admin', '$2y$10$vcXEWj/jZoDoAAa7rQocnOwUBx682J6rN1JHDtgozBCeGuw1pnBzO', 1)
ON DUPLICATE KEY UPDATE username = username;
