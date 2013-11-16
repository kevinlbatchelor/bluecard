<?php
include_once('dbconnection.php');

$con = getNewConnection();
// Create database
if ($con->query("CREATE DATABASE bluecard") === TRUE) {
    echo "A new bluecard database has been created. <a href='../index.php'>Click here to start using bluecard.</a>";
} else {
    echo "Error creating database:. <a href='../index.php'>Click here to start using bluecard.</a>" . mysqli_connect_error();
}

// Create tables

mysqli_select_db($con, "bluecard");
createTableCard($con);
createTableUsers($con);
createTableCardStatus($con);
createTableUserSession($con);
createTableFeatures($con);
$con->close();

function createTableCardStatus($con)
{
    $query = 'CREATE TABLE cardstatus (id INT NOT NULL PRIMARY KEY ,
											name VARCHAR( 255 ) NOT NULL) ENGINE = INNODB';
    $con->query($query);
    echo mysqli_connect_error();

    $insertquery = "INSERT INTO cardstatus (ID,name)
									 VALUES (10,'new')";
    $con->query($insertquery);

    $insertquery = "INSERT INTO cardstatus (ID,name)
									 VALUES (20,'accepted')";
    $con->query($insertquery);

    $insertquery = "INSERT INTO cardstatus (ID,name)
									 VALUES (30,'rejected')";
    $con->query($insertquery);

    echo mysql_error();
}

function createTableUsers($con)
{
    $users = "CREATE TABLE users (id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),

								  username VARCHAR(255) UNIQUE,
								  password VARCHAR(255),
								  type VARCHAR(255),
								  datecreated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
								  active MEDIUMINT DEFAULT TRUE) ENGINE = INNODB";

    $con->query($users);
    echo mysqli_connect_error();

    $insertusers = "INSERT INTO users (username,password,type)
							   VALUES ('admin','admin','100')";
    $con->query($insertusers);
    echo mysqli_connect_error();
}

function createTableUserSession($con)
{
    $userssession = "CREATE TABLE userssession (id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
								  username VARCHAR(255),
								  token VARCHAR(255),
								  datecreated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE = INNODB ";

    $con->query($userssession);
    echo mysqli_connect_error();
}

function createTableCard($con)
{
    $card = "CREATE TABLE card (id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
										name VARCHAR(255),
										description VARCHAR(255),
										username VARCHAR(255),
										statusId VARCHAR(255),
										votes VARCHAR(255)

										)";
    $con->query($card);
    echo mysqli_connect_error();
    echo mysql_error();
}

function createTableFeatures($con)
{
    $sql = "CREATE TABLE features (id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
								  username VARCHAR(255) UNIQUE,
								  createcard VARCHAR(255),
								  voteforcard VARCHAR(255),
								  loadcards VARCHAR(255)) ENGINE = INNODB";

    $con->query($sql);
    echo mysqli_connect_error();

    $sql = "INSERT INTO features (username,createCard,voteForCard,loadCards)  VALUES ('admin','1','1','1')";
    $con->query($sql);
}

