<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 9/27/13
 * Time: 4:46 PM
 */
include_once('../pdo/card.php');
include_once('dbconnection.php');

// access logic

if (checkToken($_REQUEST['username'], $_REQUEST['token'])) {
    if ($_REQUEST['name']) {
        $feature = "createcard";
        if (CheckRights($_REQUEST['username'], $feature)) {
            createCard($_REQUEST['name'], $_REQUEST['description'], $_REQUEST['username'], '10');
        } else {
            echo "{\"error\":\"You do not have access to this feature.\"}";
        }

    } else if ($_REQUEST['id']) {
        loadCard($_REQUEST['id']);

    } else if ($_REQUEST['loadmycard']) {
        loadMyCards($_REQUEST['loadmycard']);

    } else if ($_REQUEST['votes']) {
        $feature = "voteforcard";
        if (CheckRights($_REQUEST['username'], $feature)) {
            voteForCard($_REQUEST['thiscard']);
        } else {
            echo "[{\"error\":\"You do not have access to this feature.\"}]";
        }

    } else {
        $feature = "loadcards";
        if (CheckRights($_REQUEST['username'], $feature)) {
            loadCards();
        } else {
            echo "[{\"error\":\"You do not have access to this feature.\"}]";
        }
    }
} else {
    echo "[{\"error\":\"You are not logged in. Log in to view this data.\"}]";
}

// functions

function createCard($name = null, $description = null, $username, $statusId)
{
    if ($name) {
        $query = "INSERT INTO card (name, description, username, statusId, votes) VALUE ('" . $name . "','" . $description . "','" . $username . "','" . $statusId . "','1')";
        $con = getConnection();
        if ($con->query($query) === TRUE) {
            $card = loadCardCreated($con);
            echo json_encode($card);
        } else {
            echo "Fail:" . $con->error;
        }
    }
}

function voteForCard($id)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT votes FROM card WHERE id =:id";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("id", $id, PDO::PARAM_STR);
        $stmt->execute();
        $votes = $stmt->fetchColumn(0);
        $votes = $votes + 1;
        $sql = "UPDATE card SET votes = :votes WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("id", $id, PDO::PARAM_STR);
        $stmt->bindValue("votes", $votes, PDO::PARAM_STR);
        $stmt->execute();
        echo "{\"votes\":$votes}";
    } catch (PDOException $e) {
        $e->getMessage();
    }

}

function loadCardCreated($con)
{
    $query = "SELECT * FROM card WHERE id = " . $con->insert_id;
    if ($result = $con->query($query)) {
        $row = $result->fetch_row();
        $card = new Card($row[0], $row[1], $row[2], $row[3], $row[4]);
        $result->close();
        return $card;
    }
}

function loadCards()
{
    $query = "SELECT * FROM card WHERE id > 0";
    $con = getConnection();
    $cards = array();
    if ($result = $con->query($query)) {
        while ($row = $result->fetch_row()) {
            $card = new Card($row[0], $row[1], $row[2], $row[3], $row[4]);
            $cards[] = $card;
        }
        echo json_encode($cards);

        $result->close();
    }
}

function loadCard($id = null)
{
    $query = "SELECT * FROM card WHERE id = '" . $id . "'";
    $con = getConnection();
    if ($result = $con->query($query)) {
        while ($row = $result->fetch_row()) {
            $card = new Card($row[0], $row[1], $row[2]);
            $cards[] = $card;
        }
        $result->close();
    }
}

function checkToken($username, $token)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM userssession WHERE username =:username AND token =:token";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_INT);
        $stmt->bindValue("token", $token, PDO::PARAM_INT);
        $stmt->execute();
        $valid = $stmt->fetchColumn();
        return $valid;
    } catch (PDOException $e) {
        $e->getMessage();
    }
}

function loadMyCards($loadmycard)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM card WHERE username = :loadmycard";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("loadmycard", $loadmycard, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $card = new Card($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
            $cards[] = $card;
        }
        if ($stmt->rowCount()) {
            echo json_encode($cards);
        } else {
            echo "[{\"error\":\"No cards have your name on them.\"}]";
        }

    } catch (PDOException $e) {
        $e->getMessage();
    }
}

function CheckRights($username, $feature)
{
    try {
        $con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT $feature FROM features WHERE username = :username AND $feature=1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue("username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $access = $stmt->fetchColumn(0);
        if ($access) {
            $valid = true;
            return $valid;
        } else {
            $valid = false;
            return $valid;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

