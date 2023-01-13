-- ALPHA

CREATE TABLE IF NOT EXISTS `Users` (
    `id` INT NOT NULL AUTO_INCREMENT
	,`ucid` varchar(10) NOT NULL UNIQUE
    ,`password` VARCHAR(60) NOT NULL
    ,`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ,`modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ,PRIMARY KEY (`id`)
)

CREATE TABLE IF NOT EXISTS  `Roles`
(
    `id`         INT NOT NULL AUTO_INCREMENT
    ,`rolename`  VARCHAR(20) NOT NULL UNIQUE
    ,`created`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ,`modified`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ,PRIMARY KEY (`id`)
)

CREATE TABLE IF NOT EXISTS  `UserRoles`
(
    `id`         INT AUTO_INCREMENT NOT NULL,
    `user_id`    INT,
    `role_id`    INT,
    `created`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `modified`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`role_id`) REFERENCES Roles(`id`),
    UNIQUE KEY (`user_id`, `role_id`)
)

/*

These credentials are invalid for the latest release.

INSERT INTO Users (ucid, password)
VALUES ('sn236', '$2y$10$7I.K/1BlX9htBcGQDgHN3e1S2TQvYsAJZtBSdVfrt0tlViabQcrba'); -- volleyball

INSERT INTO Users (ucid, password)
VALUES ('tt71', '$2y$10$4YE9ntcGkWzjZQWx/y9tK.6aKx5oWHcK.jsop/vPTre0TgYTCLGje'); -- soccer

INSERT INTO Users (ucid, password)
VALUES ('pw92', '$2y$10$UrjarsagDJh3fAWiYPf0HeEfPOpcKL3ieb8Pse.eV.Ckos6WFVsvK'); -- tennis

*/

INSERT INTO Roles (id, rolename)
VALUES ('1', 'Student')

INSERT INTO Roles (rolename)
VALUES ('Teacher')

INSERT INTO UserRoles (id, user_id, role_id)
VALUES ('1', '2', '2')

INSERT INTO UserRoles (user_id, role_id)
VALUES ('1', '1')

INSERT INTO UserRoles (user_id, role_id)
VALUES ('3', '1')

/*

These credentials are invalid for the latest release.

UPDATE Users 
SET password='$2y$10$nUy69SThXDhIsafM/zuqv.GMWCLDIgGemGyTda/7axw4wFhmv8gQW'
WHERE id=3

UPDATE Users 
SET password='$2y$10$VhSzaamXO74M8eZ0SrUCH.KO9jycZLJ05mVA3tJHwtavj/o1Nn6ou'
WHERE id=2

UPDATE Users 
SET password='$2y$10$ZowMZ9RwBHMutX5ZLMFeI.eupbTfbIPn8qjwZLCDaS9Qv1E.CPyEC'
WHERE id=1

*/

----------------------------------------------------------------

-- BETA

CREATE TABLE IF NOT EXISTS `Questions` (
    `id` INT NOT NULL AUTO_INCREMENT
    ,`question` TEXT(1000) NOT NULL
    ,`difficulty` VARCHAR(10) NOT NULL
	,`topic` VARCHAR(30) NOT NULL
	,PRIMARY KEY (`id`)
	,UNIQUE KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `TestCases` (
    `id` INT NOT NULL AUTO_INCREMENT
    ,`question_id` INT NOT NULL
	,`function_name` VARCHAR(30) NOT NULL
    ,`case_1` TEXT(1000) NOT NULL
	,`case_2` TEXT(1000) NOT NULL
	,`case_3` TEXT(1000)
	,`case_4` TEXT(1000)
	,`case_5` TEXT(1000)
    ,PRIMARY KEY (`id`)
	,FOREIGN KEY (`question_id`) REFERENCES Questions(`id`)
	,UNIQUE KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `Exams` (
    `id` INT NOT NULL AUTO_INCREMENT
	,`name` VARCHAR(15) NOT NULL
    ,`question_id` INT NOT NULL
    ,`question_points` INT NOT NULL
    ,PRIMARY KEY (`id`)
	,FOREIGN KEY (`question_id`) REFERENCES Questions(`id`)
	,CHECK (`question_points` < 100)
);

CREATE TABLE IF NOT EXISTS `StudentAnswers` (
    `id` INT NOT NULL AUTO_INCREMENT /* response id */
    ,`question_id` INT NOT NULL
	,`exam_name` VARCHAR(15) NOT NULL
	,`student_answer` TEXT(1000)
	,`comments` TEXT(500)
    ,PRIMARY KEY (`id`)
	,FOREIGN KEY (`question_id`) REFERENCES Questions(`id`)
	,UNIQUE KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `ExamResults` (
    `id` INT NOT NULL AUTO_INCREMENT
    ,`question_id` INT NOT NULL
	,`exam_name` VARCHAR(15) NOT NULL
	,`student_answer_id` INT NOT NULL
    ,`question_points` INT NOT NULL -- how many points the student got in total
	,`func_name_points` INT NOT NULL -- how many points the student got for the function name
	,`case_1_points` INT NOT NULL -- how many points the student got for passing test case 1
	,`case_2_points` INT NOT NULL -- how many points the student got for passing test case 2
	,`case_3_points` INT -- how many points the student got for passing test case 3
	,`case_4_points` INT -- how many points the student got for passing test case 4
	,`case_5_points` INT -- how many points the student got for passing test case 5
    ,PRIMARY KEY (`id`)
	,FOREIGN KEY (`question_id`) REFERENCES Questions(`id`)
	,FOREIGN KEY (`student_answer_id`) REFERENCES StudentAnswers(`id`)
	,UNIQUE KEY (`id`)
	,CHECK (`question_points` <= 100)
);


/* insert queries */

--insert new question into question bank

INSERT INTO Questions (question,difficulty,topic)
VALUES('$question', '$difficulty','$topic');




/* select queries */

-- display question bank (all questions):

SELECT id, question, type, difficulty
FROM Questions;

-- display question bank (Python questions):

SELECT id, question, type, difficulty
FROM Questions
WHERE 1=1 AND language='Python'

-- display exam

SELECT E.question_id, Q.question_id, question, question_points
FROM Exams E, Questions Q
WHERE 1=1 AND E.name='name' AND E.question_id=Q.question_id

-- RELEASE CANDIDATE

CREATE TABLE IF NOT EXISTS `ExamResults` (
    `id` INT NOT NULL AUTO_INCREMENT -- "finalized response id"
    ,`question_id` INT NOT NULL
	,`exam_name` VARCHAR(15) NOT NULL
	,`student_answer_id` INT NOT NULL
    ,`auto_question_points` FLOAT NOT NULL -- how many points the student got in total for the question from the autograder
	,`auto_func_name_points` FLOAT NOT NULL -- how many points the student got for the function name from the autograder
	,`auto_func_name_comment` VARCHAR(150) NOT NULL
	,`auto_constraint_points` FLOAT
	,`auto_constraint_comment` VARCHAR(150)
	,`auto_case_1_points` FLOAT NOT NULL -- how many points the student got for passing test case 1 from the autograder
	,`auto_case_1_comment` VARCHAR(150) NOT NULL
	,`auto_case_2_points` FLOAT NOT NULL -- how many points the student got for passing test case 2 from the autograder
	,`auto_case_2_comment` VARCHAR(150) NOT NULL
	,`auto_case_3_points` FLOAT -- how many points the student got for passing test case 3 from the autograder
	,`auto_case_3_comment` VARCHAR(150)
	,`auto_case_4_points` FLOAT -- how many points the student got for passing test case 4 from the autograder
	,`auto_case_4_comment` VARCHAR(150)
	,`auto_case_5_points` FLOAT -- how many points the student got for passing test case 5 from the autograder
	,`auto_case_5_comment` VARCHAR(150)
	,`edit_question_points` FLOAT NOT NULL  -- how many points the student got in total for the question... the sum of this should be sent back as the final grade
	,`edit_func_name_points` FLOAT NOT NULL -- how many points the student got for the function name from the teacher (if not edited, this is auto_func_name_points)
	,`edit_constraint_points` FLOAT
	,`edit_case_1_points` FLOAT NOT NULL -- how many points the student got for passing test case 1 from the teacher (if not edited, this is auto_case_1_points)
	,`edit_case_2_points` FLOAT NOT NULL -- how many points the student got for passing test case 2 from the teacher (if not edited, this is auto_case_2_points)
	,`edit_case_3_points` FLOAT -- how many points the student got for passing test case 3 from the teacher (if not edited, this is auto_case_3_points)
	,`edit_case_4_points` FLOAT -- how many points the student got for passing test case 4 from the teacher (if not edited, this is auto_case_4_points)
	,`edit_case_5_points` FLOAT -- how many points the student got for passing test case 5 from the teacher (if not edited, this is auto_case_5_points)
    ,PRIMARY KEY (`id`)
	,FOREIGN KEY (`question_id`) REFERENCES Questions(`id`)
	,FOREIGN KEY (`student_answer_id`) REFERENCES StudentAnswers(`id`)
	,UNIQUE KEY (`id`)
	,CHECK (`auto_question_points` <= 100)
	,CHECK (`edit_question_points` <= 100)
);

CREATE TABLE IF NOT EXISTS `Questions_test` (
    `id` INT NOT NULL AUTO_INCREMENT
    ,`question` TEXT(1000) NOT NULL
    ,`difficulty` VARCHAR(10) NOT NULL
	,`topic` VARCHAR(30) NOT NULL
	,`q_constraint` VARCHAR(20)
	,PRIMARY KEY (`id`)
	,UNIQUE KEY (`id`)
);












