CREATE TABLE progress_trackings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_io VARCHAR(255) NOT NULL,
    project_code VARCHAR(255) NULL,
    prod_order_no VARCHAR(255) NULL,
    current_stage VARCHAR(255) NULL,
    progress_percent INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);