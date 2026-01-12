<?php

namespace App\Domain\Database;

use PDO;

/**
 * Class DBConnection
 *
 * Handles creation and reuse of a PDO database connection.
 * Uses lazy loading to avoid creating the connection until needed.
 */
final class DBConnection
{
    /**
     * Database name
     *
     * @var string
     */
    private string $dbName;

    /**
     * Database host
     *
     * @var string
     */
    private string $host;

    /**
     * Database username
     *
     * @var string
     */
    private string $username;

    /**
     * Database password
     *
     * @var string
     */
    private string $password;

    /**
     * PDO instance (lazy-loaded)
     *
     * @var PDO|null
     */
    private ?PDO $pdo = null;

    /**
     * DBConnection constructor.
     *
     * @param string $dbName
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $dbName,
        string $host,
        string $username,
        string $password
    ) {
        $this->dbName = $dbName;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Returns a PDO instance.
     * Creates the connection on first call and reuses it afterward.
     *
     * @return PDO
     */
    public function getPDO(): PDO
    {
        if ($this->pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $this->host,
                $this->dbName
            );

            $this->pdo = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }

        return $this->pdo;
    }
}
