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
            printf("There was an error with your connection: %s\n", mysqli_connect_error());
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
        if (isset($_POST["google_id"]) && isset($_POST["google_auth_token"])) {
            print_r("IIIIITS VALID\n");
            print_r("ID IS " . $_POST["google_id"] . "\n");
            print_r("TOKEN IS " . $_POST["google_auth_token"] . "\n");

            //TODO validate Google Auth token

            // Look up code in database
//            $query = "SELECT * from user_cards";
            $query = sprintf("INSERT INTO users (auth_type, google_id, coins, level)
                              VALUES('goog', %s, 100, 0)
                              ON DUPLICATE KEY UPDATE uuid=LAST_INSERT_ID(uuid)", $_POST["google_id"]);
            print "Query is " . $query . "\n";
            $result = $this->db->query($query);
            if ( !$result ) {
                echo "Error code ({$this->db->errno}) : {$this->db->error}\n";
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
            $res = $this->db->insert_id;
            printf("Returned id is %s\n", $res);
            $this->db->commit();
        } else {
            print_r("NOOOO NOT VALID\n");
            print_r(file_get_contents('php://input'));
        }
    }}

// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new Auth();
$api->signIn();