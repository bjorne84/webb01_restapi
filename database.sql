/* Create Tables*/
CREATE TABLE `education` (
  `Education_ID` INT(11),
  `School` VARCHAR(200) NOT NULL,
  `Programme` VARCHAR(100) NOT NULL,
  `Startdate` DATE NOT NULL,
  `Enddate` DATE NOT NULL
);

CREATE TABLE `courses` (
  `Course_ID` INT(11),
  `Education_ID` INT(11),
  `CourseName` VARCHAR(100) NOT NULL,
  `Points` VARCHAR (15),
  `Grade` VARCHAR(5)
);

CREATE TABLE `work_experience` (
  `CV_ID` INT(11),
  `Workplace` VARCHAR(200) NOT NULL,
  `Titel` VARCHAR (100) NOT NULL,
  `Startdate` DATE,
  `Enddate` DATE
);

CREATE TABLE `portfolio` (
  `Portfolio_ID` INT(11),
  `Titel` VARCHAR(200) NOT NULL,
  `URL` VARCHAR(150) NOT NULL,
  `Image_url` VARCHAR(200) ,
  `Description` TEXT NOT NULL
);


CREATE TABLE `language` (
  `Language_ID` INT(11),
  `Language` VARCHAR(100) NOT NULL,
  `Img_url` VARCHAR (200),
  PRIMARY KEY (`Language_ID`)
);

//
CREATE TABLE `bridge_language` (
  `Course_ID` INT(11) NOT NULL,
  `Language_ID` INT(11) NOT NULL,
  KEY `PKFK` (`Course_ID`, `Language_ID`)
);

CREATE TABLE `bridge_portfolio_language` (
  `Portfolio_ID` INT(11) NOT NULL,
  `Language_ID` INT(11) NOT NULL,
  KEY `PKFK` (`Portfolio_ID`, `Language_ID`)
);





/* Sets primary-key and auto increment
Education*/
ALTER TABLE education ADD CONSTRAINT Education_PK PRIMARY KEY (Education_ID);
ALTER TABLE education MODIFY Education_ID INTEGER AUTO_INCREMENT;

/* Courses*/
ALTER TABLE courses ADD CONSTRAINT Course_PK PRIMARY KEY (Course_ID);
ALTER TABLE courses MODIFY Course_ID INTEGER AUTO_INCREMENT;

/* Work_experience*/
ALTER TABLE work_experience ADD CONSTRAINT Work_experience_PK PRIMARY KEY (CV_ID);
ALTER TABLE work_experience MODIFY CV_ID INTEGER AUTO_INCREMENT;

/* Portfolio */
ALTER TABLE portfolio ADD CONSTRAINT Portfolio_PK PRIMARY KEY (Portfolio_ID);
ALTER TABLE portfolio MODIFY Portfolio_ID INTEGER AUTO_INCREMENT;

/* Language */
ALTER TABLE language MODIFY Language_ID INTEGER AUTO_INCREMENT;



/* ADD FK*/
/* Sets Foreign key*/
ALTER TABLE courses ADD CONSTRAINT Course_FK FOREIGN KEY (Education_ID) REFERENCES education (Education_ID);

/* Sets fk to not null*/
ALTER TABLE courses MODIFY Education_ID INT(11) NOT NULL;
