CREATE DATABASE employee_management;

USE employee_management;

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    department VARCHAR(255) NOT NULL
);
