CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('client', 'operator', 'technician', 'admin') NOT NULL DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('connect', 'repair') NOT NULL,
    status_id INT NOT NULL,
    assigned_technician_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_requests_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_requests_status FOREIGN KEY (status_id) REFERENCES statuses(id) ON DELETE RESTRICT,
    CONSTRAINT fk_requests_technician FOREIGN KEY (assigned_technician_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS request_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    status_id INT NOT NULL,
    changed_by_user INT NOT NULL,
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_history_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_history_status FOREIGN KEY (status_id) REFERENCES statuses(id) ON DELETE RESTRICT,
    CONSTRAINT fk_history_user FOREIGN KEY (changed_by_user) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS request_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO statuses (code, name) VALUES
('created', 'Создана'),
('accepted', 'Принята'),
('assigned', 'Назначена'),
('in_progress', 'В работе'),
('done', 'Выполнена'),
('closed', 'Закрыта')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (name, email, phone, password_hash, role) VALUES
('Администратор', 'admin@local.test', '+70000000001', '$2y$12$nAffMS70XT5a.AdBmKgYhuOzqKcf5ljgHSWfqx.OgG0SLlmOZsdu2', 'admin'),
('Оператор', 'operator@local.test', '+70000000002', '$2y$12$BYWy2oUk5AYWsq1YlHlHEecCTANY7u6Abbo52MB8BjrqyV.dZUDOi', 'operator'),
('Мастер', 'tech@local.test', '+70000000003', '$2y$12$HBzYVoxZf3Ewe9hDt4zAeOC7dZzR8EHUQNH7PMx48kGSoifEdmF2a', 'technician'),
('Клиент', 'client@local.test', '+70000000004', '$2y$12$W8nc25shOWBlTh5o9tzsoOwGe58wWoU7vu8CHxqZVVGID91iepaJq', 'client')
ON DUPLICATE KEY UPDATE name = VALUES(name);
