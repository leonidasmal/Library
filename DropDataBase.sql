SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS School_Unit;
DROP TABLE IF EXISTS School_Unit_Manager;
DROP TABLE IF EXISTS Book;
DROP TABLE IF EXISTS Book_Language;
DROP TABLE IF EXISTS Author;
DROP TABLE IF EXISTS Book_Author;
DROP TABLE IF EXISTS Category;
DROP TABLE IF EXISTS Book_Category;
DROP TABLE IF EXISTS Book_Keyword;
DROP TABLE IF EXISTS School_Book;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Borrower_Card;
DROP TABLE IF EXISTS administrator;
DROP TABLE IF EXISTS Register;
DROP TABLE IF EXISTS Students_Professors;
DROP TABLE IF EXISTS Copies;
DROP TABLE IF EXISTS Copy_Status;
DROP TABLE IF EXISTS Borrow;
DROP TABLE IF EXISTS Loan;
DROP TABLE  IF EXISTS Reservation;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Review_status;
SET FOREIGN_KEY_CHECKS =1;
DROP PROCEDURE IF EXISTS change_count;
DROP PROCEDURE IF EXISTS AddIntervalToDate;
DROP EVENT IF EXISTS delete_expired_reservations;
DROP VIEW if exists LateLoans;
DROP VIEW if exists Book_Details;
DROP VIEW if exists youngteachers;