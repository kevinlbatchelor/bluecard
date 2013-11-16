<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 10/21/13
 * Time: 1:59 PM
 */

class Session
{
    public $id;
    public $username;
    public $datecreated;
    public $token;
    public $createcard;
    public $voteforcard;
    public $loadcards;


    function __construct($id = null, $username = null, $token, $datecreated = null,$createcard = null, $voteforcard = null, $loadcards = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->datecreated = $datecreated;
        $this->token = $token;


        $this->createcard = $createcard;
        $this->voteforcard = $voteforcard;
        $this->loadcards = $loadcards;
    }

    function getJSON()
    {
        return '{"Session": ' . json_encode($this) . '}';
    }

}