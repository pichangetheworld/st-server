<?php
/**
 * Created by PhpStorm.
 * User: pchan
 * Date: 15/02/27
 */

// Helper method to get a string description for an HTTP status code
// From http://www.gen-x-design.com/archives/create-a-rest-api-with-php/

require_once('util.php');

class Auth {
    private $db;

    // Constructor - open DB connection
    function __construct() {
        $host = "192.168.211.191";
        $user = "pi";
        $pass = "change*";
        $db = "system_trump";
        $this->db = new mysqli($host, $user, $pass, $db);
        $this->db->autocommit(FALSE);
        if (mysqli_connect_error()) {
            error_log("There was an error with your connection: %s\n", mysqli_connect_error());
        }
    }

    // Destructor - close DB connection
    function __destruct() {
        $this->db->close();
    }

    // Main method to redeem a code
    function signIn() {
        // Print all users in database
        // Check for required parameters
        error_log("Contents are " . file_get_contents('php://input'));
        $requestBody = json_decode(file_get_contents('php://input'));

//        print_r($requestBody);
        $googleId = $requestBody->{"google_id"};
        if (isset($googleId) && isset($requestBody->{"google_auth_token"})) {
            error_log("IIIIITS VALID");
            error_log(file_get_contents('php://input'));
            error_log("ID IS " . $googleId);
            error_log("TOKEN IS " . $requestBody->{"google_auth_token"});

            //TODO validate Google Auth token

            // Look up code in database
//            $query = "SELECT * from user_cards";
            $query = sprintf("INSERT INTO users (auth_type, google_id, coins, level)
                              VALUES('goog', %s, 100, 0)
                              ON DUPLICATE KEY UPDATE uuid=LAST_INSERT_ID(uuid)", $googleId);
            error_log("Query is " . $query . "\n");
            $result = $this->db->query($query);
            if ( !$result ) {
                echo "Error code ({$this->db->errno}) : {$this->db->error}\n";
            } else {
                error_log("Query successfully changed " . $this->db->affected_rows . " rows.");
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
            $user_id = $this->db->insert_id;
            error_log("Returned id is " . $user_id);
            $this->db->commit();

            $data = sprintf('{ "_id":%s, "deck":"" }', $user_id);
            header('Content-Type: application/json');
            echo $data;
        } else {
            error_log("NOOOO NOT VALID");
            error_log(file_get_contents('php://input'));
        }
    }}

// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new Auth();
$api->signIn();