CREATE DATABASE dobu_martial_arts;
USE dobu_martial_arts;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    membership_plan ENUM('Basic', 'Intermediate', 'Advanced', 'Elite', 'Junior') NOT NULL,
    membership_expiry DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT NOT NULL
);

CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE user_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    status ENUM('Pending', 'Completed', 'Failed') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    bio TEXT
);

CREATE TABLE class_instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    instructor_id INT NOT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);

CREATE TABLE IF NOT EXISTS private_tuitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    instructor_id INT NOT NULL,
    tuition_day VARCHAR(10) NOT NULL,
    tuition_time TIME NOT NULL,
    duration INT NOT NULL DEFAULT 2, -- Duration in hours, default to 2
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);

CREATE TABLE IF NOT EXISTS specialist_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_type VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO classes (name, description) VALUES
('Karate', 'Japanese martial art focusing on striking techniques'),
('Judo', 'Modern Japanese martial art emphasizing throws and grappling'),
('Muay Thai', 'Thai boxing with powerful strikes and clinching techniques'),
('Jiu-Jitsu', 'Grappling-based martial art focusing on ground fighting');

-- Update the schedules table entries
DELETE FROM schedules;

-- Morning slot: 06:00 - 07:30
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(4, 'Monday', '06:00:00', '07:30:00'), -- Jiu-jitsu
(1, 'Tuesday', '06:00:00', '07:30:00'), -- Karate
(2, 'Wednesday', '06:00:00', '07:30:00'), -- Judo
(4, 'Thursday', '06:00:00', '07:30:00'), -- Jiu-jitsu
(3, 'Friday', '06:00:00', '07:30:00'); -- Muay Thai

-- Mid-morning slot: 08:00 - 10:00
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(3, 'Monday', '08:00:00', '10:00:00'), -- Muay Thai
(NULL, 'Tuesday', '08:00:00', '10:00:00'), -- Private Tuition
(NULL, 'Wednesday', '08:00:00', '10:00:00'), -- Private Tuition
(NULL, 'Thursday', '08:00:00', '10:00:00'), -- Private Tuition
(4, 'Friday', '08:00:00', '10:00:00'); -- Jiu-jitsu

-- Late morning slot: 10:30 - 12:00
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(NULL, 'Monday', '10:30:00', '12:00:00'), -- Private Tuition
(NULL, 'Tuesday', '10:30:00', '12:00:00'), -- Private Tuition
(NULL, 'Wednesday', '10:30:00', '12:00:00'), -- Private Tuition
(NULL, 'Thursday', '10:30:00', '12:00:00'), -- Private Tuition
(NULL, 'Friday', '10:30:00', '12:00:00'), -- Private Tuition
(2, 'Saturday', '10:30:00', '12:00:00'), -- Judo
(1, 'Sunday', '10:30:00', '12:00:00'); -- Karate

-- Early afternoon slot: 13:00 - 14:30
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(NULL, 'Monday', '13:00:00', '14:30:00'), -- Open Mat/Personal Practice
(NULL, 'Tuesday', '13:00:00', '14:30:00'), -- Open Mat/Personal Practice
(NULL, 'Wednesday', '13:00:00', '14:30:00'), -- Open Mat/Personal Practice
(NULL, 'Thursday', '13:00:00', '14:30:00'), -- Open Mat/Personal Practice
(NULL, 'Friday', '13:00:00', '14:30:00'), -- Open Mat/Personal Practice
(1, 'Saturday', '13:00:00', '14:30:00'), -- Karate
(2, 'Sunday', '13:00:00', '14:30:00'); -- Judo

-- Afternoon slot: 15:00 - 17:00
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(4, 'Monday', '15:00:00', '17:00:00'), -- Kids Jiu-jitsu
(2, 'Tuesday', '15:00:00', '17:00:00'), -- Kids Judo
(1, 'Wednesday', '15:00:00', '17:00:00'), -- Kids Karate
(4, 'Thursday', '15:00:00', '17:00:00'), -- Kids Jiu-jitsu
(2, 'Friday', '15:00:00', '17:00:00'), -- Kids Judo
(3, 'Saturday', '15:00:00', '17:00:00'), -- Muay Thai
(4, 'Sunday', '15:00:00', '17:00:00'); -- Jiu-jitsu

-- Evening slot: 17:30 - 19:00
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 'Monday', '17:30:00', '19:00:00'), -- Karate
(3, 'Tuesday', '17:30:00', '19:00:00'), -- Muay Thai
(2, 'Wednesday', '17:30:00', '19:00:00'), -- Judo
(4, 'Thursday', '17:30:00', '19:00:00'), -- Jiu-jitsu
(3, 'Friday', '17:30:00', '19:00:00'); -- Muay Thai

-- Late evening slot: 19:00 - 21:00
INSERT INTO `schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(4, 'Monday', '19:00:00', '21:00:00'), -- Jiu-jitsu
(2, 'Tuesday', '19:00:00', '21:00:00'), -- Judo
(4, 'Wednesday', '19:00:00', '21:00:00'), -- Jiu-jitsu
(1, 'Thursday', '19:00:00', '21:00:00'), -- Karate
(NULL, 'Friday', '19:00:00', '21:00:00'); -- Private Tuition

-- Insert all six instructors
INSERT INTO instructors (name, email, bio) VALUES
('Mauricio Gomez', 'mauricio@dobumartialarts.com', 'Gym owner and head martial arts coach. Coaches in all martial arts, 4th dan blackbelt judo, 3rd Dan Blackbelt jiu-jitsu, 1st dan blackbelt karate, accredited muay thai coach'),
('Sarah Nova', 'sarah@dobumartialarts.com', 'Assistant martial arts coach, 5th Dan Karate'),
('Guy Victory', 'guy@dobumartialarts.com', 'Assistant martial arts coach, 2nd Dan Blackbelt jiu-jitsu, 1st Dan blackbelt judo'),
('Morris Davis', 'morris@dobumartialarts.com', 'Assistant martial arts coach, accredited Muay Thai Coach, 3rd Dan Blackbelt karate'),
('Traci Santiago', 'traci@dobumartialarts.com', 'Fitness Coach, BSc in Sports Science, Qualified in health and nutrition, Specialises in devising strength and conditioning programs for combat athletes'),
('Harpreet Kaur', 'harpreet@dobumartialarts.com', 'Fitness Coach, BSc in Physiotherapy, MSc in Sport Science');

-- Link instructors to classes (update this section as needed)
INSERT INTO class_instructors (class_id, instructor_id) VALUES
(1, 1), (1, 2), (1, 4), -- Karate
(2, 1), (2, 3), -- Judo
(3, 1), (3, 4), -- Muay Thai
(4, 1), (4, 3); -- Jiu-Jitsu
