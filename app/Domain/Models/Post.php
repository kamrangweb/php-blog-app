<?php

namespace App\Domain\Models;

use DateTime;
use Exception;
use PDO;

class Post extends Model
{
    protected $table = 'posts';

    /**
     * Base query for posts
     */
    private function basePostQuery(): string
    {
        return "
            SELECT 
                p.id,
                p.title,
                p.body,
                p.image_path,
                p.created_at,

                u.id AS user_id,
                u.username AS username,

                c.id AS category_id,
                c.category AS category_name

            FROM posts p

            JOIN users u 
                ON u.id = p.user_id

            LEFT JOIN categories c 
                ON c.id = p.category_id
        ";
    }

    /**
     * Find single post
     */
    public function findById(int $id): ?array
    {
        $sql = $this->basePostQuery() . "
            WHERE p.id = ?
        ";

        $post = $this->query($sql, [$id], true);

        return $post ?: null;
    }

    /**
     * Get all posts
     */
    public function all(): array
    {
        $sql = $this->basePostQuery() . "
            ORDER BY p.id DESC
        ";

        return $this->query($sql);
    }

    /**
     * Paginated posts
     */
    public function getPostsPage(int $limit, int $offset): array
    {
        $sql = $this->basePostQuery() . "
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->getPDO()->prepare($sql);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Format created date
     */
    public function getCreatedAt(): string
    {
        return (new DateTime($this->created_at))
            ->format('d/m/Y h:ia');
    }

    /**
     * Format updated date
     */
    public function getUpdatedAt(): string
    {
        return isset($this->updated_at)
            ? (new DateTime($this->updated_at))->format('d/m/Y h:ia')
            : '';
    }

    /**
     * Short body preview
     */
    public function getExcerpt(): string
    {
        return mb_strimwidth($this->body, 0, 100, "...");
    }

    /**
     * Get all tags
     */
    public function getAllTags(): array
    {
        return $this->query("
            SELECT *
            FROM tags
            ORDER BY id DESC
        ");
    }

    /**
     * Create post with tags
     */
    public function create(array $data, ?array $relations = null): bool
    {
        $pdo = $this->db->getPDO();

        try {

            $pdo->beginTransaction();

            $created = parent::create($data);

            if (!$created) {
                $pdo->rollBack();
                return false;
            }

            $postId = (int) $pdo->lastInsertId();

            /**
             * Insert tags
             */
            if (!empty($relations)) {

                $values = [];
                $params = [];

                foreach ($relations as $tagId) {
                    $values[] = "(?, ?)";
                    $params[] = $postId;
                    $params[] = $tagId;
                }

                $sql = "
                    INSERT INTO post_tag (post_id, tag_id)
                    VALUES " . implode(',', $values);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }

            $pdo->commit();

            return true;

        } catch (Exception $e) {

            $pdo->rollBack();

            return false;
        }
    }

    /**
     * Update post with tags
     */
    public function update(array &$data, ?array $relations = null): bool
    {
        $pdo = $this->db->getPDO();

        try {

            $pdo->beginTransaction();

            $updated = parent::update($data);

            if (!$updated) {
                $pdo->rollBack();
                return false;
            }

            $postId = $this->id;

            /**
             * Remove old relations
             */
            $stmt = $pdo->prepare("
                DELETE FROM post_tag
                WHERE post_id = ?
            ");

            $stmt->execute([$postId]);

            /**
             * Insert new relations
             */
            if (!empty($relations)) {

                $values = [];
                $params = [];

                foreach ($relations as $tagId) {
                    $values[] = "(?, ?)";
                    $params[] = $postId;
                    $params[] = $tagId;
                }

                $sql = "
                    INSERT INTO post_tag (post_id, tag_id)
                    VALUES " . implode(',', $values);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }

            $pdo->commit();

            return true;

        } catch (Exception $e) {

            $pdo->rollBack();

            return false;
        }
    }

    /**
     * Get all tags with selected state
     */
    public function getTags(int $postId): array
    {
        return $this->query(
            "
            SELECT 
                t.id,
                t.tag,
                t.created_at,

                CASE
                    WHEN pt.tag_id IS NOT NULL THEN true
                    ELSE false
                END AS is_selected

            FROM tags t

            LEFT JOIN post_tag pt
                ON pt.tag_id = t.id
                AND pt.post_id = ?

            ORDER BY t.created_at DESC
            ",
            [$postId]
        );
    }

    /**
     * Get tags of single post
     */
    public function getTagsOfPost(int $postId): array
    {
        return $this->query(
            "
            SELECT t.*

            FROM tags t

            JOIN post_tag pt
                ON pt.tag_id = t.id

            WHERE pt.post_id = ?

            ORDER BY t.created_at DESC
            ",
            [$postId]
        );
    }

    /**
     * Get posts by user
     */
    public function getPostsByUserId(int $userId): array
    {
        return $this->query(
            "
            SELECT
                p.id,
                p.title,
                p.body,
                p.image_path,
                p.created_at

            FROM posts p

            WHERE p.user_id = ?

            ORDER BY p.id DESC
            ",
            [$userId]
        );
    }
}
