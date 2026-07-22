CREATE DATABASE IF NOT EXISTS gpcms;
USE gpcms;

CREATE TABLE IF NOT EXISTS roles (
  role_id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  login_id VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  mobile_number VARCHAR(15) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (role_name) VALUES
  ('Citizen'),
  ('Gram Sevak'),
  ('Field Officer'),
  ('Super Admin')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
SELECT 'Citizen User', 'citizen', '$2y$10$BBufOcaelyG8M4WL5wDvsO3i8kujh6qYjbH07ysAUHGcmL/LYkgZO', role_id, '9999999999', 'active'
FROM roles WHERE role_name = 'Citizen'
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
SELECT 'Gram Sevak User', 'gramsevak', '$2y$10$4sUl14R27ngS6ks1076B0ecwdEU3fRYoOWqudUgnsfQNkJyrP7NLm', role_id, '8888888888', 'active'
FROM roles WHERE role_name = 'Gram Sevak'
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
SELECT 'Field Officer User', 'fieldofficer', '$2y$10$fA/3ur5d81c9itWyZu7fc.YZs7nmDLNtDeG4l/YExgQ66.TU6vZcK', role_id, '7777777777', 'active'
FROM roles WHERE role_name = 'Field Officer'
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
SELECT 'Super Admin User', 'superadmin', '$2y$10$OBWzmUt6HMWKSr9qO1nCbea/FEXQ2M7bvx8agnTtz9xcB5n23ICxW', role_id, '6666666666', 'active'
FROM roles WHERE role_name = 'Super Admin'
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);
