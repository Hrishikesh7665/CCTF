<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"]."/404.html");
    exit();
}

$ctf_Dates = []; // add date format date month year (always last date will be treated as current date)
$currentCTF = end($ctf_Dates);

// First Db Configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "cdac-k_ctf";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// End First Db Configuration

$debug_mode = false;
// $ldap_connection = false;
$platformLink = "https://ctf.cdac.in";

// This part is not required when ldap connection is not in use
$ldap_hostname = "";
$ldapBaseDn = "";
$ldapPort = 389;
$ldap_protocol = 3;
$ldap_rootDN = null; // The DN for the ROOT Account Set to null for anonymous LDAP binding
$ldap_root_password = null;
$ldap_uft8 = true;

$ldap_filter = '(objectClass=*)';

$key = "";

function handle_error($e)
{
    global $debug_mode;
    if ($debug_mode) {
        die('debug: ' . $e->getMessage());
    } else {
        echo 'error';
        die();
    }
}

class ArchiveDbConnection {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "cdac-k_ctf_archive";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            // throw new Exception("Connection failed: " . $this->conn->connect_error);
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn instanceof mysqli) {
            $this->conn->close();
        }
    }

    // Check if a table exists in the database
    private function tableExists($table_name) {
        $query = "SHOW TABLES LIKE '$table_name'";
        $result = $this->conn->query($query);
        if ($result === false) {
            throw new Exception("Error checking if table exists: " . $this->conn->error);
        }
        return $result->num_rows > 0;
    }

    // Create a table using the SQL template if it doesn't already exist
    public function createTableFromTemplate($table_name) {
        $template_path = $_SERVER["DOCUMENT_ROOT"] . "/assets/SQL Template/cdac-k_ctf_archive.sql";
        // Check if the table already exists
        if ($this->tableExists($table_name)) {
            throw new Exception("Table '$table_name' already exists.");
        }

        // Read the SQL template file
        $sql_template = file_get_contents($template_path);
        if ($sql_template === false) {
            throw new Exception("Error reading SQL template file: '$template_path'");
        }

        // Replace the placeholder with the actual table name
        $sql = str_replace('{{table_name}}', $table_name, $sql_template);

        // Execute the SQL
        if (!$this->conn->multi_query($sql)) {
            throw new Exception("Error creating table: " . $this->conn->error);
        }

        // Ensure all queries are processed
        do {
            if ($result = $this->conn->store_result()) {
                $result->free();
            }
        } while ($this->conn->next_result());

        // Table created successfully
        return "Table '$table_name' created successfully.";
    }
}


class SecureSMTP
{
    private $host;
    private $username;
    private $password;
    private $smtpSecure;
    private $port;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        // Set your SMTP configuration details securely
        $this->host = 'smtp.cdac.in';
        $this->username = '';
        $this->password = '';
        $this->smtpSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $this->port = 587;
        $this->fromEmail = '';
        $this->fromName = 'CDAC CTF';
    }

    public function configureMailer($mail)
    {
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->SMTPAuth = true;
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        $mail->SMTPSecure = $this->smtpSecure;
        $mail->Port = $this->port;
        $mail->setFrom($this->fromEmail, $this->fromName);
    }
}

?>
