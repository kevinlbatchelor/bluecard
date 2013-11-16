<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 11/4/13
 * Time: 3:02 PM
 */

include_once('../pdo/card.php');
include_once('../dbconnection.php');

if(checkToken($_REQUEST['username'], $_REQUEST['token'])){
     deleteCard($_REQUEST['id']);
    } else {
    echo"{error:{'error':'Incorrect user or token.'}}";
}

function deleteCard($id)
{
    $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "Delete FROM card WHERE id =:id";
    $stmt = $con->prepare($sql);
    $stmt->bindValue("id", $id, PDO::PARAM_INT);
    echo "{item deleted}";
    $stmt->execute();
}

function checkToken($username, $token)
{
    $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM userssession WHERE username =:username AND token =:token";
    $stmt = $con->prepare($sql);
    $stmt->bindValue("username", $username, PDO::PARAM_INT);
    $stmt->bindValue("token", $token, PDO::PARAM_INT);
    $stmt->execute();
    $valid = $stmt->fetchColumn();
    return $valid;
}
