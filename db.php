<?php
// ** COPYRIGHT NOTICE: THIS CODE CANNOT BE USED FOR COMMERCIAL USE WITHOUT A LICENCE **
// ** Contact: admin@livenewsnow.org **
class connectDB{
    
    private $con;
    private $status = false;

    public function __construct(){
        
        $host = 'localhost';
        $dbname = 'crm';
        $dbuser = '';
        $dbpass = '';

        $mysqli = new mysqli($host, $dbuser, $dbpass, $dbname);
        $this->con = $mysqli;
        $this->status = true;

    }

    public function get_status(){
        return $this->status;
    }
    public function get_connection(){
        return $this->con;
    }
}
?>