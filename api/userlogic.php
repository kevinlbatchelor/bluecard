<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 10/16/13
 * Time: 4:02 PM
 */
include_once("dbconnection.php");
include_once("../pdo/user.php");
include_once("../pdo/session.php");

if ($_REQUEST['compassword']) {
    register($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['compassword']);

} else if ($_REQUEST['logout']) {
    logout($_REQUEST['logout'], $_REQUEST['token']);

} else {
    userLogin($_REQUEST['username'], $_REQUEST['password']);
    //set up access here?
}

function register($username = null, $password = null, $compassword = null)
{
    $salt = "Zo4rU5Z1YyKJAASY0PT6EUg7BBYdlEhPaNLuxAwU8lqu1ElzHv0Ri7EM6irpx5w";
    if ($password == $compassword) {

        try {
            $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO users(username, password,type) VALUES(:username, :password,'10')";
            $stmt = $con->prepare($sql);
            $stmt->bindValue("username", $username, PDO::PARAM_STR);
            $stmt->bindValue("password", hash("sha256", $password . $salt), PDO::PARAM_STR);
            $stmt->execute();
            $user = loadUser($username);
            echo $user->getJSON();
        } catch (PDOException $e) {
            echo '{"Error": ' . json_encode($e->getMessage()) . '}';
        }
    } else {
        echo "{Error:{password and confirm do not match}}";
    }
}

function loadUser($username)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE username =:username";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_BOTH);
        $user = new User($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
        return $user;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function userLogin($username = null, $password = null)
{
    $success = false;
    $salt = "Zo4rU5Z1YyKJAASY0PT6EUg7BBYdlEhPaNLuxAwU8lqu1ElzHv0Ri7EM6irpx5w";
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_STR);
        $stmt->bindValue("password", hash("sha256", $password . $salt), PDO::PARAM_STR);
        $stmt->execute();
        $valid = $stmt->fetchColumn();

        if ($valid) {
            $success = true;
            startLogin($username);
        } else {
            echo "{Error:{Incorrect Username/Password}}";
        }
        $con = null;
        return $success;
    } catch (PDOException $e) {
        echo '{"Error": ' . json_encode($e->getMessage()) . '}';
        return $success;
    }
}

function startLogin($username)
{
    if (checkSession($username)) {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $date = new DateTime();
        $token = hash("sha256", $date->format('Y-m-d H:i:s'));
        $sql = "UPDATE userssession SET token = '$token' WHERE username = :username";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $session = loadSession($username);
        echo $session->getJSON();
    } else {
        // create a new token
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $date = new DateTime();
        $token = hash("sha256", $date->format('Y-m-d H:i:s'));
        $sql = "INSERT INTO userssession(username,token)  VALUES (:username,'$token')";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $session = loadSession($username);
        echo $session->getJSON();
    }
}

function loadSession($username)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql1 = "SELECT * FROM features WHERE username =:username";
        $stmt1 = $con->prepare($sql1);
        $stmt1->bindValue("username", $username, PDO::PARAM_INT);
        $stmt1->execute();
        $row1 = $stmt1->fetch(PDO::FETCH_BOTH);


        $sql = "SELECT * FROM userssession WHERE username =:username";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_INT);
        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_BOTH);
        $session = new Session($row[0], $row[1], $row[2], $row[3], $row1['createcard'],$row1['voteforcard'],$row1['loadcards']);
        return $session;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function checkSession($username)
{
    $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM userssession WHERE username =:username";
    $stmt = $con->prepare($sql);
    $stmt->bindValue("username", $username, PDO::PARAM_INT);
    $stmt->execute();
    $valid = $stmt->fetchColumn();
    return $valid;
}

function logout($username, $token)
{
    $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "DELETE FROM userssession WHERE username =:username AND token =:token";
    $stmt = $con->prepare($sql);
    $stmt->bindValue("username", $username, PDO::PARAM_INT);
    $stmt->bindValue("token", $token, PDO::PARAM_INT);
    $stmt->execute();
}

function loadFeatures($username)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM features WHERE username =:username";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_BOTH);
        $features = new Features($row[0], $row[1], $row[2], $row[3], $row[4]);
        return $features;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}