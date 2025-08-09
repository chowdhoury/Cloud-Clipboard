<?php
// filepath: d:\Programming\Web Programming 2.0\Course_Project\config\database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'cloud_storage';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Check if PDO MySQL driver is available
            if (!extension_loaded('pdo_mysql')) {
                throw new Exception('PDO MySQL extension is not loaded. Please enable it in php.ini');
            }
            
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
        } catch(PDOException $exception) {
            throw new Exception("Database connection error: " . $exception->getMessage());
        } catch(Exception $e) {
            throw new Exception("Configuration error: " . $e->getMessage());
        }
        
        return $this->conn;
    }
    
    // Test connection method
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            return $conn !== null;
        } catch(Exception $e) {
            return false;
        }
    }
}
?>