<?php

namespace App\Domain\Models;

use App\Domain\Database\DBConnection;
use PDO;

/**
 * Class Model
 * @package App\Domain\Models
 *
 * @property int $id
 */
abstract class Model
{
    /**
     * @var DBConnection
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param DBConnection $db
     */
    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        return $this->query("SELECT * FROM ".$this->table." ORDER BY created_at DESC");
    }


    public function findById(int $id)
    {
        $model = $this->query("SELECT * FROM ".$this->table." WHERE id = ?", [$id], true);

        return $model ?: false;
    }

    public function create(array $data): bool
    {
        $columns = "";
        $bindings = "";
        $i = 1;

        foreach ($data as $key => $value) {
            $comma = $i === count($data) ? "" : ", ";
            $columns .= "$key".$comma;
            $bindings .= ":$key".$comma;
            $i++;
        }

        $columns = trim($columns);
        $bindings = trim($bindings);

        $sql = "INSERT INTO ".$this->table." (".$columns.") VALUES (".$bindings.")";

        return $this->query($sql, $data);
    }


    protected function update(array &$data): bool
    {
        $data['id'] = $this->id;

        $columns = "";
        $i = 1;

        foreach ($data as $key => $value) {
            $comma = $i === count($data) ? "" : ", ";
            $columns .= "$key = :$key".$comma;
            $i++;
        }

        $columns = trim($columns);

        $sql = "UPDATE ".$this->table." SET ".$columns." WHERE id = :id";

        return $this->query($sql, $data);
    }


    public function destroy(): bool
    {
        return $this->query("DELETE FROM ".$this->table." WHERE id = ?", [$this->id]);
    }


    protected function updateTimestamps(): bool
    {
        $sql = "UPDATE ".$this->table." SET updated_at = NOW() WHERE id = ?";

        return $this->query($sql, [$this->id]);
    }



    protected function query(string $sql, ?array $params = null, ?bool $single = null)
    {
        $method = is_null($params) ? 'query' : 'prepare';
        $stmt = $this->db->getPDO()->$method($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_class($this), [$this->db]);

        if (
            strpos($sql, 'INSERT') === 0
            || strpos($sql, 'UPDATE') === 0
            || strpos($sql, 'DELETE') === 0
        ) {
            return $stmt->execute($params);
        }

        // print($params['limit']);

        if ($method === 'prepare') {
            // $stmt->bindValue(':limit', $params['limit'], PDO::PARAM_INT);
            // $stmt->bindValue(':offset', $params['offset'], PDO::PARAM_INT);

            // $stmt->execute();
            $stmt->execute($params);
        }

        $fetch = is_null($single) ? 'fetchAll' : 'fetch';
        $result = $stmt->$fetch();


        return $result ? $result : array();

    }
}