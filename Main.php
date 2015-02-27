<?php
/**
 * Created by PhpStorm.
 * User: pchan
 * Date: 15/02/27
 */

// Helper method to get a string description for an HTTP status code
// From http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
function getStatusCodeMessage($status)
{
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = Array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    return (isset($codes[$status])) ? $codes[$status] : '';
}

// Helper method to send a HTTP response code/message
function sendResponse($status = 200, $body = '', $content_type = 'text/html')
{
    $status_header = 'HTTP/1.1 ' . $status . ' ' . getStatusCodeMessage($status);
    header($status_header);
    header('Content-type: ' . $content_type);
    echo $body;
}

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
            print_r("ID IS " . $_POST["uuid"] . '\n');
            print_r("TOKEN IS " . $_POST["auth_token"] . '\n');
            print_r("DATA IS " . $_POST["data"] . '\n');
            // Put parameters into local variables
            $uuid = $_POST["uuid"];
            $entityBody = $_POST["data"];
            $json = json_decode($entityBody, true);
            print_r($json);
            $auth_token = $_POST["auth_token"];

            // Look up code in database
            $query = "'UPDATE user_cards SET deck_count = (case";
            foreach ($json as $key=>$value) {
                $query .= sprintf("when card_id=%s then %s",$key,$value);
//                     		when card_id=1 then 4
            }
            $query .= sprintf("end)	WHERE user_id=%s'",$uuid);
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            while ($stmt->fetch()) {
                break;
            }
            $stmt->close();
        } else {
            print_r("NOOOO NOT VALID\n");
            print_r("ID IS " . $_POST["uuid"] . '\n');
            print_r("ID IS " . $_POST["auth_token"] . '\n');
            print_r(file_get_contents('php://input'));
        }
    }}

// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new Users();
$api->updateDeck();