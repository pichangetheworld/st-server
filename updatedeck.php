<?php
/**
 * Created by PhpStorm.
 * User: pchan
 * Date: 15/02/27
 */

// Helper method to get a string description for an HTTP status code
// From http://www.gen-x-design.com/archives/create-a-rest-api-with-php/

require_once('util.php');

class DeckEditor{
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
            error_log("There was an error with your connection: %s\n", mysqli_connect_error());
        }
    }

    // Destructor - close DB connection
    function __destruct() {
        $this->db->close();
    }

    // Curl Method for testing:
    /*
     * curl -d "uuid=200" -d "auth_token=1" -d "data[]={\"id\": 1,\"count\" : 13}&data[]={\"id\":2,\"count\":1}" "http://192.168.211.191:3000/updatedeck.php"
     */
    // Main method to update the deck
    function updateDeck() {
        error_log("Contents are " . file_get_contents('php://input'));
        $requestBody = json_decode(file_get_contents('php://input'));

        $userId = $requestBody->{"uuid"};
        $authToken = $requestBody->{"auth_token"};
        // Check for required parameters
        if (isset($userId) && isset($authToken)) {
            error_log("Update Deck is VALID");
            error_log("ID IS " . $userId);
            error_log("TOKEN IS " . $authToken);
            // Put parameters into local variables
            $cardData = $requestBody->{"data"};
//            $auth_token = $_POST["auth_token"];

            // Look up code in database
//            $query = "SELECT * from user_cards";
            $query = "UPDATE user_cards ";
            $query .= "SET deck_count = (case ";
            foreach ($cardData as $item) {
//                var_dump($item);
                $query .= sprintf("when card_id=%s then %s ",$item->{'id'},$item->{'count'});
//                     		when card_id=1 then 4
            }
            $query .= "else deck_count ";
            $query .= "end)";
            $query .= sprintf(" WHERE user_id=%s",$userId);
            error_log("Query is " . $query);
            $result = $this->db->query($query);
            if ( !$result ) {
                echo "Error code ({$this->db->errno}) : {$this->db->error}";
            } else {
                error_log("Query successfully changed " . $this->db->affected_rows . " rows.");
//                echo 'Query successfully changed ' . mysqli_affected_rows . " rows." . "\n";
                // TODO return success
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
            error_log("Update Deck is NOT VALID");
            error_log(file_get_contents('php://input'));
        }
    }}

// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new DeckEditor();
$api->updateDeck();