<?php
/**
 * Created by PhpStorm.
 * User: pchan
 * Date: 15/02/27
 */

// Helper method to get a string description for an HTTP status code
// From http://www.gen-x-design.com/archives/create-a-rest-api-with-php/

require_once('util.php');

class Users {
    private $db;

    // Constructor - open DB connection
    function __construct() {
        $host = "localhost";
        $user = "pi";
        $pass = "change*";
        $db = "system_trump";
        $this->db = new mysqli($host, $user, $pass, $db);
        $this->db->autocommit(FALSE);
        if (mysqli_connect_error()) {
            echo 'There was an error with your connection: '. mysqli_connect_error();
        } else {
            print("Constructor, db got created " . $this->db->info . "\n");
        }
    }

    // Destructor - close DB connection
    function __destruct() {
        $this->db->close();
    }

    // Main method to redeem a code
    function updateDeck() {
        // Print all users in database
//        $stmt = $this->db->prepare('SELECT uuid, auth_type, created_at FROM users');
//        $stmt->execute();
//        $stmt->bind_result($uuid, $auth_type, $created_at);
//        while ($stmt->fetch()) {
//            echo "$uuid was created at $created_at!<br>";
//        }
//        $stmt->close();
        // Check for required parameters
        if (isset($_POST["uuid"]) && isset($_POST["auth_token"])) {
            print_r("IIIIITS VALID\n");
            print_r("ID IS " . $_POST["uuid"] . "\n");
            print_r("TOKEN IS " . $_POST["auth_token"] . "\n");
            // Put parameters into local variables
            $uuid = $_POST["uuid"];
            $entityBody = $_POST["data"];
//            $auth_token = $_POST["auth_token"];

            // Look up code in database
//            $query = "SELECT * from user_cards";
            $query = "UPDATE user_cards ";
            $query .= "SET deck_count = (case ";
            foreach ($entityBody as $pair) {
                print_r("Considering pair " . $pair . "\n");
                $item = json_decode($pair, true);
                $query .= sprintf("when card_id=%s then %s ",$item['id'],$item['count']);
//                     		when card_id=1 then 4
            }
            $query .= "else deck_count ";
            $query .= "end)";
            $query .= sprintf(" WHERE user_id=%s",$uuid);
            print "Query is " . $query . "\n";
            $result = $this->db->query($query);
            if ( !$result ) {
                echo "Error code ({$this->db->errno}) : {$this->db->error}";
            } else {
                printf("Query successfully changed %d rows.\n", $this->db->affected_rows);
//                echo 'Query successfully changed ' . mysqli_affected_rows . " rows." . "\n";
            }
//            if ($result->num_rows > 0) {
//                // output data of each row
//                while($row = $result->fetch_assoc()) {
////                    printf("result %s %s %s %s\n", $user_id, $card_id, card_count, deck_count);
//                    echo "id: " . $row["user_id"]. " - Name: " . $row["card_id"]. " " . $row["card_count"] . " " . $row["deck_count"] . "\n";
//                }
//            } else {
//                echo "0 results";
//            }
            $this->db->commit();
        } else {
            print_r("NOOOO NOT VALID\n");
            print_r("ID IS " . $_POST["uuid"] . "\n");
            print_r("ID IS " . $_POST["auth_token"] . "\n");
            print_r(file_get_contents('php://input'));
        }
    }}

// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new Users();
$api->updateDeck();