<?php

declare(strict_types=1);

namespace App\Domain\Database;

use PDO;
use PDOException;

/**
 * Provides a reusable lazy-loaded PDO connection.
 */
final readonly class DBConnection
{
    public function __construct(
        private string $dbName,
        private string $host,
        private string $username,
        private string $password,
        private int $port = 3306
    ) {
    }

    private ?PDO $pdo = null;

    /**
     * Returns a shared PDO instance.
     */
    public function getPDO(): PDO
    {
        return $this->pdo ??= $this->createConnection();
    }

    /**
     * Creates a new PDO connection.
     */
    private function createConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->port,
            $this->dbName
        );

        try {
            return new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => true,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException(
                'Database connection failed.',
                (int) $e->getCode(),
                $e
            );
        }
    }
}
