<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 9/27/13
 * Time: 4:23 PM
*/

class Card
{
    public $name;
    public $description;

    function __construct($id = null, $name = null, $description = null, $username=null, $statusId=null, $votes=null)
    {
        $this-> id = $id;
        $this-> name = $name;
        $this-> description = $description;
        $this-> username = $username;
        $this-> statusid = $statusId;
        $this-> votes = $votes;
    }

    function getJSON()
    {
        return '{"Card": ' . json_encode($this) . '}';
    }

}