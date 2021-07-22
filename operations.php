<?php
class Operations{
    private $name;
    private $description;
    private $year;
    private $conn;
    private $rowID;

    public function connect(){
        $this->conn = new mysqli('localhost', 'root', '', 'internet_tehnologije');
        
        if(isset($_POST['name']))
            $this->name = $this->conn->real_escape_string($_POST['name']);

        if(isset($_POST['description']))
            $this->description = $this->conn->real_escape_string($_POST['description']);
        
        if(isset($_POST['year']))
            $this->year = $this->conn->real_escape_string($_POST['year']);
        
        if(isset($_POST['rowID']))
            $this->rowID = $this->conn->real_escape_string($_POST['rowID']);
    }

    // Create
    public function insert(){
        $this->connect();
        $sql = $this->conn->query("SELECT id FROM films WHERE name = '$this->name'AND year = '$this->year'");
        if ($sql->num_rows > 0)
            exit("Film with this name and year already exists!");
        else {
            $this->conn->query("INSERT INTO films (name, description, year) 
                        VALUES ('$this->name', '$this->description', '$this->year')");
            exit('success');
        }
    }

    // Read
    public function readAll(){
        $this->connect();
        $start = $this->conn->real_escape_string($_POST['start']);
        $limit = $this->conn->real_escape_string($_POST['limit']);

        $sql = $this->conn->query("SELECT id, name FROM films LIMIT $start, $limit");
        if ($sql->num_rows > 0) {
            $response = "";
            while($data = $sql->fetch_array()) {
                $response .= '
                    <tr>
                        <td>'.$data["id"].'</td>
                        <td id="film_'.$data["id"].'">'.$data["name"].'</td>
                        <td>
                            <input type="button" onclick="viewORedit('.$data["id"].', \'edit\')" value="Edit" class="btn btn-primary">
                            <input type="button" onclick="viewORedit('.$data["id"].', \'view\')" value="View" class="btn">
                            <input type="button" onclick="deleteRow('.$data["id"].')" value="Delete" class="btn btn-danger">
                        </td>
                    </tr>
                ';
            }
            exit($response);
        } else
            exit('reachedMax');
    }

    public function readById(){
        $this->connect();
        $rowID = $this->conn->real_escape_string($_POST['rowID']);
        $sql = $this->conn->query("SELECT name, description, year FROM films WHERE id='$this->rowID'");
        $data = $sql->fetch_array();
        $jsonArray = array(
            'name' => $data['name'],
            'year' => $data['year'],
            'description' => $data['description'],
        );

        exit(json_encode($jsonArray));
    }

    // Update
    public function update(){
        $this->connect();
        $this->conn->query("UPDATE films SET name='$this->name', description='$this->description', year='$this->year' WHERE id='$this->rowID'");
        exit('success');
    }

    // Delete
    public function delete(){
        $this->connect();
        $this->conn->query("DELETE FROM films WHERE id='$this->rowID'");
        exit('The film has been deleted!');
    }

    // Login
    public function login(){
        $this->connect();

        $email = $this->conn->real_escape_string($_POST['email']);
        $password = md5($this->conn->real_escape_string($_POST['password']));

        $data = $this->conn -> query("SELECT id FROM users WHERE email='$email' AND password='$password'");

        if($data -> num_rows > 0){
            $_SESSION['loggedIn'] = '1';
            $_SESSION['email'] = $email;
            exit('success');
        }else{
            exit('error');
        }
    }

    // Logout
    public function logout(){
        session_start();

        unset($_SESSION['loggedIn']);
        session_destroy();
        exit('success');
    }
}
?>