DROP DATABASE IF EXISTS StudyGroupSystem;
CREATE DATABASE StudyGroupSystem;
USE StudyGroupSystem;

CREATE TABLE Student (
    student_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gsuite_email VARCHAR(100) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Admin') DEFAULT 'Student',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Course (
    course_code VARCHAR(20) PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE StudyGroup (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(100) NOT NULL,
    visibility VARCHAR(10) NOT NULL DEFAULT 'Public',
    course_code VARCHAR(20) NOT NULL,
    created_by VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT CK_StudyGroup_visibility CHECK (visibility IN ('Public','Private')),
    CONSTRAINT FK_StudyGroup_course FOREIGN KEY (course_code) REFERENCES Course(course_code) ON DELETE CASCADE,
    CONSTRAINT FK_StudyGroup_creator FOREIGN KEY (created_by) REFERENCES Student(student_id) ON DELETE CASCADE,
    CONSTRAINT UQ_StudyGroup UNIQUE (course_code, group_name)
);

CREATE TABLE JoinRequest (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    group_id INT NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'Pending',
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT CK_JoinRequest_status CHECK (status IN ('Pending','Accepted','Rejected')),
    CONSTRAINT FK_JR_student FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
    CONSTRAINT FK_JR_group FOREIGN KEY (group_id) REFERENCES StudyGroup(group_id) ON DELETE CASCADE,
    CONSTRAINT UQ_JR UNIQUE (student_id, group_id)
);

CREATE TABLE GroupMember (
    student_id VARCHAR(20),
    group_id INT,
    joining_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_points INT DEFAULT 0,
    role VARCHAR(20) DEFAULT 'General Member',

    PRIMARY KEY (student_id, group_id),
    CONSTRAINT CK_role CHECK (role IN ('Admin','General Member')),
    CONSTRAINT FK_GM_student FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
    CONSTRAINT FK_GM_group FOREIGN KEY (group_id) REFERENCES StudyGroup(group_id) ON DELETE CASCADE
);

CREATE TABLE Resource (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    type VARCHAR(20) DEFAULT 'Other',
    url_path TEXT,
    group_id INT,
    uploaded_by VARCHAR(20) NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT CK_resource_type CHECK (type IN ('PDF','Video','Document','Image','Link','Other')),
    CONSTRAINT FK_res_group FOREIGN KEY (group_id) REFERENCES StudyGroup(group_id) ON DELETE CASCADE,
    CONSTRAINT FK_res_member FOREIGN KEY (uploaded_by) REFERENCES Student(student_id) ON DELETE SET NULL,
    CONSTRAINT UQ_resource_title UNIQUE (group_id, title)
);

CREATE TABLE StudySession (
    session_id INT AUTO_INCREMENT UNIQUE,
    group_id INT,
    session_number INT,
    session_date DATE,
    session_time TIME,
    duration INT,
    topic VARCHAR(200) UNIQUE,
    platform_link TEXT,

    PRIMARY KEY (session_number, topic),
    CONSTRAINT FK_session_group FOREIGN KEY (group_id) REFERENCES StudyGroup(group_id) ON DELETE CASCADE,
    CONSTRAINT UQ_session UNIQUE (group_id, session_number)
);

CREATE TABLE Attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT,
    student_id VARCHAR(20),
    status VARCHAR(10),
    marked_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT CK_attendance CHECK (status IN ('Present','Absent')),
    CONSTRAINT FK_att_session FOREIGN KEY (session_id) REFERENCES StudySession(session_id) ON DELETE CASCADE,
    CONSTRAINT FK_att_member FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
    CONSTRAINT UQ_att UNIQUE (session_id, student_id)
);

CREATE TABLE Notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    type VARCHAR(20),
    message TEXT,
    related_group_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT FK_notif_student FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE
);

CREATE INDEX idx_group_course ON StudyGroup(course_code);
CREATE INDEX idx_member_group ON GroupMember(group_id);

INSERT INTO Student (student_id, name, gsuite_email, department, password, role) VALUES
('2021001','Kabya Rahman','kabya@g.bracu.ac.bd','CSE','pass', 'Student'),
('2021002','Ariana','ariana@g.bracu.ac.bd','CSE','pass', 'Student'),
('2021003','Mahir Ahmed','mahir.ahmed@g.bracu.ac.bd','CSE','pass', 'Student'),
('2021004','Nadia Sultana','nadia.sultana@g.bracu.ac.bd','EEE','pass', 'Student'),
('2021005','Rafiq Hasan','rafiq.hasan@g.bracu.ac.bd','CSE','pass', 'Student'),
('2021006','Tamanna Akter','tamanna@g.bracu.ac.bd','BBA','pass', 'Student'),
('2021007','Farhan Sadiq','farhan@g.bracu.ac.bd','CSE','pass', 'Student'),
('2021008','Sumaiya Rahman','sumaiya@g.bracu.ac.bd','EEE','pass', 'Student'),
('admin','System Admin','admin@syncup.com','IT','admin123', 'Admin');

INSERT INTO Course (course_code, course_name) VALUES
('CSE370','Database Systems'),
('CSE331','Data Structures'),
('CSE221','Algorithms'),
('EEE361','Signal Processing'),
('BBA301','Business Analytics');

INSERT INTO StudyGroup (group_name, course_code, created_by) VALUES
('DB Warriors','CSE370','2021001'),
('SQL Masters','CSE370','2021003'),
('Data Explorers','CSE331','2021002'),
('Algorithm Aces','CSE221','2021005');

INSERT INTO GroupMember (student_id, group_id, role, total_points) VALUES
('2021001', 1, 'Admin', 50),
('2021002', 1, 'General Member', 30),
('2021003', 2, 'Admin', 40),
('2021004', 2, 'General Member', 20),
('2021005', 4, 'Admin', 45),
('2021006', 3, 'General Member', 15),
('2021007', 1, 'General Member', 20),
('2021008', 1, 'General Member', 10),
('2021001', 2, 'General Member', 10);

INSERT INTO Resource (title, type, url_path, group_id, uploaded_by) VALUES
('Updated SQL Guide', 'PDF', 'https://example.com/sql-guide.pdf', 1, '2021001'),
('ER Diagram Tutorial', 'Link', 'https://example.com/er-diagrams', 1, '2021002'),
('Normalization Notes', 'Document', 'https://example.com/normalization.docx', 2, '2021003'),
('Sorting Algorithms Video', 'Video', 'https://youtube.com/watch?v=sorting', 3, '2021006'),
('Graph Theory PDF', 'PDF', 'https://example.com/graph-theory.pdf', 4, '2021005');

INSERT INTO StudySession (group_id, session_number, session_date, session_time, duration, topic, platform_link) VALUES
(1, 1, '2026-04-20', '15:00:00', 60, 'Advanced Normalization', 'https://meet.google.com/abc-defg-hij'),
(1, 2, '2026-04-25', '16:00:00', 90, 'SQL Joins & Subqueries', 'https://meet.google.com/xyz-abcd-efg'),
(1, 3, '2026-04-28', '15:00:00', 60, 'Triggers & Stored Procedures', 'https://meet.google.com/db-triggers-101'),
(2, 1, '2026-04-22', '14:00:00', 45, 'Relational Algebra', 'https://zoom.us/j/123456789'),
(3, 1, '2026-04-21', '10:00:00', 60, 'Binary Search Trees', 'https://meet.google.com/bst-tree-101'),
(1, 4, '2026-05-02', '15:00:00', 60, 'Indexing and Hashing', 'https://meet.google.com/idx-hash'),
(1, 5, '2026-05-05', '16:00:00', 90, 'Query Optimization', 'https://meet.google.com/qry-opt'),
(1, 6, '2026-05-08', '14:00:00', 45, 'Concurrency Control', 'https://zoom.us/j/123456789'),
(1, 7, '2026-05-10', '10:00:00', 60, 'NoSQL Databases Intro', 'https://meet.google.com/nosql-101'),
(1, 8, '2026-05-15', '11:00:00', 75, 'Distributed Databases', 'https://zoom.us/j/987654321');

INSERT INTO Attendance (session_id, student_id, status) VALUES
(1, 2021001, 'Present'),
(1, 2021002, 'Present'),
(1, 2021007, 'Present'),
(1, 2021008, 'Absent'),
(2, 2021001, 'Present'),
(2, 2021002, 'Absent'),
(2, 2021007, 'Present'),
(2, 2021008, 'Present'),
(3, 2021001, 'Present'),
(3, 2021002, 'Present'),
(3, 2021007, 'Absent'),
(3, 2021008, 'Present'),
(4, 2021003, 'Present'),
(4, 2021004, 'Present'),
(4, 2021001, 'Present'),
(5, 2021006, 'Present'),
(6, 2021005, 'Present'),
(7, 2021001, 'Present'),
(7, 2021002, 'Present'),
(7, 2021007, 'Present'),
(7, 2021008, 'Present'),
(8, 2021001, 'Present'),
(8, 2021002, 'Absent'),
(8, 2021007, 'Present'),
(8, 2021008, 'Absent'),
(9, 2021001, 'Absent'),
(9, 2021002, 'Present'),
(9, 2021007, 'Absent'),
(9, 2021008, 'Present'),
(10, 2021001, 'Present'),
(10, 2021002, 'Present'),
(10, 2021007, 'Absent'),
(10, 2021008, 'Absent'),
(11, 2021001, 'Present'),
(11, 2021002, 'Absent'),
(11, 2021007, 'Present'),
(11, 2021008, 'Present');

INSERT INTO JoinRequest (student_id, group_id, status) VALUES
('2021004', 1, 'Pending'),
('2021006', 2, 'Pending'),
('2021003', 1, 'Accepted');

DELIMITER //
CREATE TRIGGER trg_attendance_points
AFTER INSERT ON Attendance
FOR EACH ROW
BEGIN
    IF NEW.status = 'Present' THEN
        UPDATE GroupMember
        SET total_points = total_points + 10
        WHERE student_id = NEW.student_id;
    END IF;
END; //

CREATE TRIGGER trg_one_admin_insert
BEFORE INSERT ON GroupMember
FOR EACH ROW
BEGIN
    IF NEW.role = 'Admin' THEN
        IF (SELECT COUNT(*) FROM GroupMember WHERE group_id = NEW.group_id AND role = 'Admin') > 0 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A group can only have one Admin';
        END IF;
    END IF;
END; //

CREATE TRIGGER trg_one_admin_update
BEFORE UPDATE ON GroupMember
FOR EACH ROW
BEGIN
    IF NEW.role = 'Admin' AND OLD.role != 'Admin' THEN
        IF (SELECT COUNT(*) FROM GroupMember WHERE group_id = NEW.group_id AND role = 'Admin') > 0 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A group can only have one Admin';
        END IF;
    END IF;
END; //
DELIMITER ;

SELECT c.course_name, COUNT(sg.group_id) as total_groups
FROM Course c
LEFT JOIN StudyGroup sg ON c.course_code = sg.course_code
GROUP BY c.course_code;

SELECT * FROM Course
WHERE course_code IN (
    SELECT course_code FROM StudyGroup
);

SELECT * FROM Course ORDER BY course_name ASC;

UPDATE Course SET course_name = TRIM(course_name);

SELECT sg.group_name, c.course_name
FROM StudyGroup sg
JOIN Course c ON sg.course_code = c.course_code;

SELECT * FROM StudyGroup
WHERE created_by IN (
    SELECT student_id FROM Student
);

SELECT * FROM StudyGroup ORDER BY created_at DESC;

UPDATE StudyGroup SET visibility = 'Public' WHERE visibility IS NULL;

SELECT s.name, sg.group_name
FROM Student s
JOIN GroupMember gm ON s.student_id = gm.student_id
JOIN StudyGroup sg ON gm.group_id = sg.group_id;

SELECT * FROM Student
WHERE department IN (
    SELECT department FROM Student WHERE role = 'Admin'
);

SELECT * FROM Student ORDER BY created_at DESC;

UPDATE Student SET name = TRIM(name);

SELECT s.name, sg.group_name, jr.status
FROM JoinRequest jr
JOIN Student s ON jr.student_id = s.student_id
JOIN StudyGroup sg ON jr.group_id = sg.group_id;

SELECT * FROM JoinRequest
WHERE group_id IN (
    SELECT group_id FROM StudyGroup
);

UPDATE JoinRequest SET status = 'Rejected' WHERE status IS NULL;

DELETE FROM JoinRequest WHERE status IS NULL;

SELECT s.name, gm.role
FROM GroupMember gm
JOIN Student s ON gm.student_id = s.student_id;

SELECT * FROM GroupMember
WHERE group_id IN (
    SELECT group_id FROM StudyGroup
);

UPDATE GroupMember SET total_points = total_points + group_id;

DELETE FROM GroupMember WHERE total_points IS NULL;

SELECT r.title, s.name as uploader_name
FROM Resource r
LEFT JOIN Student s ON r.uploaded_by = s.student_id;

SELECT * FROM Resource
WHERE group_id IN (
    SELECT group_id FROM StudyGroup
);

UPDATE Resource SET type = 'Document' WHERE type IS NULL;

DELETE FROM Resource WHERE url_path IS NULL;

SELECT ss.topic, sg.group_name
FROM StudySession ss
JOIN StudyGroup sg ON ss.group_id = sg.group_id;

SELECT * FROM StudySession
WHERE group_id IN (
    SELECT group_id FROM StudyGroup
);

UPDATE StudySession SET duration = duration + session_number;

DELETE FROM StudySession WHERE platform_link IS NULL;

SELECT s.name, a.status
FROM Attendance a
JOIN Student s ON a.student_id = s.student_id;

SELECT * FROM Attendance
WHERE session_id IN (
    SELECT session_id FROM StudySession
);

UPDATE Attendance SET status = 'Present' WHERE status IS NULL;

DELETE FROM Attendance WHERE marked_at IS NULL;

SELECT s.name, gm.total_points
FROM GroupMember gm
JOIN Student s ON gm.student_id = s.student_id
ORDER BY gm.total_points DESC;

SELECT * FROM GroupMember
WHERE total_points = (
    SELECT MAX(total_points) FROM GroupMember
);

UPDATE GroupMember SET total_points = total_points + group_id WHERE role = 'Admin';

SELECT student_id, total_points FROM GroupMember ORDER BY total_points DESC;
