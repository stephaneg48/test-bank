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

INSERT INTO Users (ucid, password)
VALUES ('sn236', '$2y$10$7I.K/1BlX9htBcGQDgHN3e1S2TQvYsAJZtBSdVfrt0tlViabQcrba'); /* volleyball* */

INSERT INTO Users (ucid, password)
VALUES ('tt71', '$2y$10$4YE9ntcGkWzjZQWx/y9tK.6aKx5oWHcK.jsop/vPTre0TgYTCLGje'); /* soccer */

INSERT INTO Users (ucid, password)
VALUES ('pw92', '$2y$10$UrjarsagDJh3fAWiYPf0HeEfPOpcKL3ieb8Pse.eV.Ckos6WFVsvK'); /* tennis */

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

UPDATE Users 
SET password='$2y$10$nUy69SThXDhIsafM/zuqv.GMWCLDIgGemGyTda/7axw4wFhmv8gQW'
WHERE id=3

UPDATE Users 
SET password='$2y$10$VhSzaamXO74M8eZ0SrUCH.KO9jycZLJ05mVA3tJHwtavj/o1Nn6ou'
WHERE id=2

UPDATE Users 
SET password='$2y$10$ZowMZ9RwBHMutX5ZLMFeI.eupbTfbIPn8qjwZLCDaS9Qv1E.CPyEC'
WHERE id=1

/*$ucid="sn236";
$password="Soccer"; 

$hash = password_hash($password, PASSWORD_BCRYPT);
echo $hash;*/

/* need to use ucid and password w/ real values first so database can have it already... */



