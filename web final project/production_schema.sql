-- ====================================================================
-- DHAKA METRO RAIL MANAGEMENT SYSTEM (MRT LINE-6)
-- PRODUCTION MYSQL DATABASE SCHEMA & INITIAL DATA
-- Compatible with: TiDB Cloud, Aiven MySQL, Railway MySQL, AWS RDS, Local XAMPP
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. USERS TABLE
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `RoleID` INT NOT NULL DEFAULT 3 COMMENT '1=Administrator, 2=Employee/Station Staff, 3=Passenger',
  `FirstName` VARCHAR(100) NOT NULL,
  `LastName` VARCHAR(100) NOT NULL DEFAULT '',
  `Mobile` VARCHAR(20) NOT NULL UNIQUE,
  `NID` VARCHAR(30) DEFAULT NULL,
  `DateOfBirth` DATE DEFAULT NULL,
  `Gender` VARCHAR(20) DEFAULT NULL,
  `Password` VARCHAR(255) NOT NULL,
  `Status` VARCHAR(20) NOT NULL DEFAULT 'Active',
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`RoleID`),
  INDEX `idx_users_mobile` (`Mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. STATIONS TABLE
CREATE TABLE IF NOT EXISTS `stations` (
  `StationID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `StationName` VARCHAR(100) NOT NULL UNIQUE,
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ROUTES TABLE
CREATE TABLE IF NOT EXISTS `routes` (
  `RouteID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `FromStation` VARCHAR(100) NOT NULL,
  `ToStation` VARCHAR(100) NOT NULL,
  `Fare` DECIMAL(10,2) NOT NULL,
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_route` (`FromStation`, `ToStation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. WALLET TABLE
CREATE TABLE IF NOT EXISTS `wallet` (
  `WalletID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NOT NULL UNIQUE,
  `Balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_wallet_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TICKETS TABLE
CREATE TABLE IF NOT EXISTS `tickets` (
  `TicketID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NOT NULL,
  `RouteID` INT NOT NULL,
  `JourneyDate` DATE NOT NULL,
  `Status` VARCHAR(50) NOT NULL DEFAULT 'Confirmed',
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_tickets_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  CONSTRAINT `fk_tickets_route` FOREIGN KEY (`RouteID`) REFERENCES `routes` (`RouteID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. PAYMENTS TABLE
CREATE TABLE IF NOT EXISTS `payments` (
  `PaymentID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `TicketID` INT NOT NULL,
  `Amount` DECIMAL(10,2) NOT NULL,
  `PaymentDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `Status` VARCHAR(50) NOT NULL DEFAULT 'Success',
  CONSTRAINT `fk_payments_ticket` FOREIGN KEY (`TicketID`) REFERENCES `tickets` (`TicketID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TRANSACTIONS TABLE
CREATE TABLE IF NOT EXISTS `transactions` (
  `TransactionID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NOT NULL,
  `Amount` DECIMAL(10,2) NOT NULL,
  `Type` VARCHAR(20) NOT NULL,
  `Reference` VARCHAR(100) NOT NULL,
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. SCHEDULES TABLE
CREATE TABLE IF NOT EXISTS `schedules` (
  `ScheduleID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `TrainName` VARCHAR(100) NOT NULL,
  `RouteID` INT DEFAULT NULL,
  `DepartureTime` TIME NOT NULL,
  `ArrivalTime` TIME NOT NULL,
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. TICKET VALIDATIONS TABLE
CREATE TABLE IF NOT EXISTS `ticket_validations` (
  `ValidationID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `TicketID` INT NOT NULL,
  `Status` VARCHAR(50) NOT NULL DEFAULT 'Valid',
  `ValidatedBy` INT DEFAULT NULL,
  `ValidationTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- INITIAL PRODUCTION DATA: MRT LINE-6 STATIONS
-- ====================================================================
INSERT IGNORE INTO `stations` (`StationName`) VALUES
('Uttara North'),
('Uttara Center'),
('Uttara South'),
('Pallabi'),
('Mirpur-11'),
('Mirpur-10'),
('Kazipara'),
('Shewrapara'),
('Agargaon'),
('Bijoy Sarani'),
('Farmgate'),
('Karwan Bazar'),
('Shahbagh'),
('Dhaka University'),
('Secretariat'),
('Motijheel'),
('Kamalapur');

-- ====================================================================
-- OFFICIAL FARES BETWEEN MRT LINE-6 STATIONS
-- ====================================================================
INSERT IGNORE INTO `routes` (`FromStation`, `ToStation`, `Fare`) VALUES
('Uttara North', 'Uttara Center', 20.00),
('Uttara North', 'Uttara South', 20.00),
('Uttara North', 'Pallabi', 30.00),
('Uttara North', 'Mirpur-11', 30.00),
('Uttara North', 'Mirpur-10', 40.00),
('Uttara North', 'Kazipara', 40.00),
('Uttara North', 'Shewrapara', 50.00),
('Uttara North', 'Agargaon', 60.00),
('Uttara North', 'Bijoy Sarani', 60.00),
('Uttara North', 'Farmgate', 70.00),
('Uttara North', 'Karwan Bazar', 80.00),
('Uttara North', 'Shahbagh', 80.00),
('Uttara North', 'Dhaka University', 90.00),
('Uttara North', 'Secretariat', 90.00),
('Uttara North', 'Motijheel', 100.00),
('Uttara North', 'Kamalapur', 100.00),

('Agargaon', 'Uttara North', 60.00),
('Agargaon', 'Mirpur-10', 30.00),
('Agargaon', 'Farmgate', 30.00),
('Agargaon', 'Shahbagh', 40.00),
('Agargaon', 'Motijheel', 50.00),
('Agargaon', 'Kamalapur', 60.00),

('Motijheel', 'Uttara North', 100.00),
('Motijheel', 'Pallabi', 80.00),
('Motijheel', 'Mirpur-10', 60.00),
('Motijheel', 'Agargaon', 50.00),
('Motijheel', 'Farmgate', 30.00),
('Motijheel', 'Shahbagh', 20.00),
('Motijheel', 'Secretariat', 20.00),

('Mirpur-10', 'Uttara North', 40.00),
('Mirpur-10', 'Agargaon', 30.00),
('Mirpur-10', 'Farmgate', 30.00),
('Mirpur-10', 'Motijheel', 60.00),

('Farmgate', 'Uttara North', 70.00),
('Farmgate', 'Agargaon', 30.00),
('Farmgate', 'Motijheel', 30.00),
('Farmgate', 'Dhaka University', 20.00);

-- Initial Line Schedules
INSERT IGNORE INTO `schedules` (`ScheduleID`, `TrainName`, `RouteID`, `DepartureTime`, `ArrivalTime`) VALUES
(1, 'MRT-01 (Red Line)', 1, '07:30:00', '08:05:00'),
(2, 'MRT-02 (Green Line)', 2, '08:00:00', '08:45:00'),
(3, 'MRT-03 (Blue Line)', 3, '09:00:00', '09:40:00');
