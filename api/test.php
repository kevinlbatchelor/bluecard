<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 11/14/13
 * Time: 5:37 PM
 */

echo "Test user logic<br>";
include_once("dbconnection.php");
include_once("../pdo/user.php");
include_once("../pdo/session.php");

CheckRights('admin','loadcards');

function CheckRights($username, $feature)
{
    try{
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT $feature FROM features WHERE username = :username AND $feature=1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $access=$stmt->fetchColumn(0);

        if($access){
            $valid=true;
            echo $valid;
            return $valid;

        }else{
            $valid=false;
            echo $valid;
            return $valid;
        }
    }catch (PDOException $e){
        echo $e->getMessage();
    }
}