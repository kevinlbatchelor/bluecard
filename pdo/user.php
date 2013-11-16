<?php
/**
 * Created by IntelliJ IDEA.
 * User: kbatchelor
 * Date: 10/21/13
 * Time: 1:59 PM
 */

class User{

    public $username;
    public $password;
    public $type;
    public $datecreated;
    public $active;

    function __construct($id=null, $username=null, $password=null,$type=type, $datecreated=null, $active=null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->type = $type;
        $this->datecreated = $datecreated;
        $this->active = $active;
    }

    function getJSON()
    {
    return '{"User": ' . json_encode($this) . '}';
    }

}