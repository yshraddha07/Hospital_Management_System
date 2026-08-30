CREATE DATABASE IF NOT EXISTS hospital_management;
USE hospital_management;

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20),
    phone VARCHAR(20),
    address TEXT,
    blood_group VARCHAR(10),
    department VARCHAR(100)
);

CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    phone VARCHAR(20)
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100),
    doctor_name VARCHAR(100),
    appointment_time TIME,
    status VARCHAR(30) DEFAULT 'Waiting'
);

CREATE TABLE opd_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100),
    status VARCHAR(30) DEFAULT 'Waiting'
);

CREATE TABLE emergency_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100),
    patient_condition VARCHAR(150),
    priority INT DEFAULT 3,
    status VARCHAR(30) DEFAULT 'Waiting'
);

CREATE TABLE medical_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    diagnosis VARCHAR(150),
    treatment VARCHAR(150),
    doctor VARCHAR(100)
);

CREATE TABLE beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(20),
    room_type VARCHAR(50),
    status VARCHAR(30) DEFAULT 'Available'
);

CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100),
    amount DECIMAL(10,2),
    status VARCHAR(30) DEFAULT 'Unpaid'
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    hod VARCHAR(100)
);

INSERT INTO doctors (name, specialization, phone) VALUES
('Dr. Rajesh Sharma','General Physician','9876500001'),
('Dr. Neha Verma','Cardiologist','9876500002'),
('Dr. Amit Mehta','Orthopedic','9876500003'),
('Dr. Pooja Singh','Neurologist','9876500004');

INSERT INTO patients (name, age, gender, phone, address, blood_group, department) VALUES
('Ramesh Kumar',45,'Male','9876543210','Mumbai','B+','Cardiology'),
('Priya Sharma',32,'Female','9876543211','Mumbai','O+','General Medicine'),
('Arjun Patel',28,'Male','9876543212','Mumbai','A+','Orthopedics');

INSERT INTO beds (room_no, room_type, status) VALUES
('101','General','Occupied'),
('102','General','Available'),
('201','Private','Available'),
('ICU-1','ICU','Occupied');

INSERT INTO departments (name, hod) VALUES
('Cardiology','Dr. Neha Verma'),
('Orthopedics','Dr. Amit Mehta'),
('Neurology','Dr. Pooja Singh'),
('General Medicine','Dr. Rajesh Sharma');

INSERT INTO appointments (patient_name, doctor_name, appointment_time) VALUES
('Ramesh Kumar','Dr. Neha Verma','10:00:00'),
('Priya Sharma','Dr. Rajesh Sharma','11:00:00');

INSERT INTO opd_queue (patient_name) VALUES
('Ramesh Kumar'),('Priya Sharma'),('Arjun Patel');

INSERT INTO emergency_queue (patient_name, patient_condition, priority) VALUES
('Suresh Yadav','Critical',1),
('Anita Sharma','Serious',2),
('Rahul Mehta','Stable',3);

INSERT INTO bills (patient_name, amount, status) VALUES
('Ramesh Kumar',2500,'Paid'),
('Priya Sharma',1800,'Unpaid');
