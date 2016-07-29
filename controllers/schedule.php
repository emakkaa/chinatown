<?php

class Schedule extends Controller{
	function __construct($action)
	{
		parent::__construct($action);
		$this->setModel('schedules');
	}

		function defaultAction()
	{
		$this->showAll();
	}
	
	function showAll()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "chinatown";
		$conn = new mysqli($servername, $username, $password,$dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "SELECT * FROM `schedule`";
		$result=$conn->query($sql);
		$this->_template->setView('schedule');
		$array = array(
			"result"=>$result,
			"title"=>'Schedule'
		);
	    $this->_template->render($array);
	}
	function showColors()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "chinatown";
		$conn = new mysqli($servername, $username, $password,$dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "SELECT * FROM colorpicker as cp 
				LEFT JOIN schedule as s 
				ON cp.schedule_id=s.id";
				$result=$conn->query($sql);
		$this->_template->setView('schedule');
		$array = array(
			"result"=>$result,
			"title"=>'Schedule'
		);
	    $this->_template->render($array);
	}
	
	function deleteRow(){
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "chinatown";
		$conn = new mysqli($servername, $username, $password,$dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		if(isset($_GET['deletedRow']) and is_numeric($_GET['deletedRow']))
		{
			$delete = $_GET['deletedRow'];
			$sql = "DELETE FROM `schedule` WHERE id = ".$delete;
			$result = $conn->query($sql);
			$sql = "DELETE FROM `colorpicker` WHERE schedule_id = ".$delete;
			$result=$conn->query($sql);
		}
		
		$this->showAll();
	}

    /**
     *
     */
    function add(){
        $group=$_POST['group_number'];
        $check=true;
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "chinatown";
        $conn = new mysqli($servername, $username, $password,$dbname);
        $sql = "SELECT group_number FROM schedule";
        $result=$conn->query($sql);
        while($row=$result->fetch_assoc())
        {
            if($group==$row['group_number']){
                $check=false;
            }
        }
        if($check){
            $this->_model->loadFrom($_POST);
            $this->_model->save();
            $this->showAll();
        }
        else{
            $this->showAll();
            echo "<script>alert('Already added schedule for this group!')</script>";
        }
    }
    function save(){
        $check=true;
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "chinatown";
        $conn = new mysqli($servername, $username, $password,$dbname);
        $sql="SELECT group_number FROM schedule";
        $result=$conn->query($sql);
        while($row=$result->fetch_assoc()){
            if($row['group_number']==$_POST['new-group']){
                $check=false;
                break;
            }
        }
        if($check){
            $sql="UPDATE schedule SET name='".$_POST['new-name']."',group_number='".$_POST['new-group'].
                "' WHERE id=".$_POST['id'];
            $result=$conn->query($sql);
            $this->showAll();
        }
        else{
            $this->showAll();
            echo "<script>alert('Already added schedule for this group!')</script>";
        }
    }
    function copy(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "chinatown";
        $conn = new mysqli($servername, $username, $password,$dbname);
        $sql="INSERT INTO schedule (name) 
              SELECT name
              FROM schedule 
              WHERE id=".$_GET['copiedid'];
        $result=$conn->query($sql);
        $id=$conn->insert_id;
        $sql="SELECT * FROM colorpicker WHERE schedule_id=".$_GET['copiedid'];
        $result=$conn->query($sql);
        $data=array();
        $colorpicker = new Colorpickers();
        while($row=$result->fetch_assoc()){
            $colorpicker->properties['id']=null;
            $data['rgb']=$row['rgb'];
            $data['hex']=$row['hex'];
            $data['schedule_id']=$id;
            $data['time']=$row['time'];
            $data['position']=$row['position'];
            $colorpicker->loadFrom($data);
            $colorpicker->save();
        }

        $this->showAll();

    }
}	