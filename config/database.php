<?php
/**
 * Conexão PDO centralizada.
 * Em produção (Railway/Heroku), lê variáveis de ambiente.
 * Localmente, usa valores do XAMPP/WAMP.
 */
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $port;

    public function __construct() {
        $this->host     = getenv('DB_HOST') ?: 'localhost';
        $this->dbname   = getenv('DB_NAME') ?: 'petamigo';   // ← nome real do seu BD
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->port     = getenv('DB_PORT') ?: '3306';
    }

    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $conn = new PDO($dsn, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conn;
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
}