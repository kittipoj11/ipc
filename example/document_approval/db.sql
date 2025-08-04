-- สร้างตารางทั้งหมด
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100)
);

CREATE TABLE workflows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    next_workflow_id INT NULL,
    FOREIGN KEY (next_workflow_id) REFERENCES workflows(id) ON DELETE SET NULL
);

CREATE TABLE workflow_steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workflow_id INT NOT NULL,
    step_number INT NOT NULL,
    approver_user_id INT NOT NULL,
    FOREIGN KEY (workflow_id) REFERENCES workflows(id),
    FOREIGN KEY (approver_user_id) REFERENCES users(id),
    UNIQUE(workflow_id, step_number)
);

CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workflow_id INT NOT NULL,
    data JSON,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    current_step INT DEFAULT 0,
    current_approver_id INT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (workflow_id) REFERENCES workflows(id),
    FOREIGN KEY (current_approver_id) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE approval_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    document_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(20) NOT NULL,
    comments TEXT,
    action_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES documents(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ใส่ข้อมูลเริ่มต้น
INSERT INTO users (id, username, full_name) VALUES
(1, 'admin', 'Admin User'),
(2, 'approver1', 'Approver One'),
(3, 'approver2', 'Approver Two'),
(4, 'approver3', 'Approver Three'),
(4, 'final_approver', 'Final Approver');

INSERT INTO workflows (id, name, next_workflow_id) VALUES
(1, 'Document A Approval', 2),
(2, 'Document B Verification', NULL);

INSERT INTO workflow_steps (workflow_id, step_number, approver_user_id) VALUES
-- Steps for Document A
(1, 1, 2), -- Approver 1
(1, 2, 3), -- Approver 2
(1, 3, 5), -- Final Approver

-- Steps for Document B
(2, 1, 2), -- Approver 1 (Verified)
(2, 2, 3), -- Approver 2
(2, 3, 4), -- Approver 3
(2, 4, 5); -- Final Approver

-- สร้างเอกสารตัวอย่างเริ่มต้นโดย Admin (user_id=1)
INSERT INTO documents (workflow_id, data, status, created_by) VALUES
(1, '{"title":"Sample Document A","amount":5000}', 'draft', 1);