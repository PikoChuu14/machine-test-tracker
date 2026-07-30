CREATE DATABASE IF NOT EXISTS machine_issue_tracker
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE machine_issue_tracker;

CREATE TABLE machines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    machine_code VARCHAR(50) NOT NULL UNIQUE,
    machine_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL
);

CREATE TABLE issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    machine_id INT NOT NULL,
    issue_title VARCHAR(150) NOT NULL,
    issue_description TEXT NOT NULL,
    status ENUM('Open', 'In Progress', 'Resolved') DEFAULT 'Open',
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_issue_machine
        FOREIGN KEY (machine_id)
        REFERENCES machines(id)
);

INSERT INTO machines (
    machine_code,
    machine_name,
    location
)
VALUES
    ('ASRS-01', 'Automated Storage and Retrieval System', 'Warehouse'),
    ('CV-01', 'Main Conveyor', 'Production Line'),
    ('PR-01', 'Label Printer', 'Packing Area');