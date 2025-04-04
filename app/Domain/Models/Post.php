<?php

namespace App\Domain\Models;

use DateTime;
use Exception;
use PDO;


class Post extends Model
{

    protected $table = 'posts';


    public function findById(int $id)
    {
        $model = $this->query("SELECT p.id, p.title, p.body, p.image_path, p.created_at, u.id as user_id, u.username as username, c.id as category_id , c.category as category_name
         FROM posts p 
         join users u on u.id = p.user_id
         left join categories c on c.id = p.category_id
         where p.id = ?", [$id], true);

        return $model ?: false;
    }

    public function all(): array
    {
        return $this->query("SELECT p.id, p.title, p.body, p.image_path, p.created_at, u.id as user_id, u.username as username, c.id as category_id , c.category as category_name
         FROM posts p
         join users u on u.id = p.user_id 
         left join categories c on c.id = p.category_id
         ORDER BY p.id desc");



    }


    public function getPostsPage(int $limit, int $offset): array
{
    $sql = "
        SELECT p.id, p.title, p.body, p.image_path, p.created_at, 
               u.id as user_id, u.username as username, 
               c.id as category_id, c.category as category_name
        FROM posts p
        JOIN users u ON u.id = p.user_id 
        LEFT JOIN categories c ON c.id = p.category_id
        ORDER BY p.id DESC
        LIMIT $limit OFFSET $offset
    ";

    // $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    // $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // $stmt->execute();

    return $this->query($sql);

}

    


 
    public function getCreatedAt(): string
    {
        return (new DateTime($this->created_at))->format('d/m/Y  h:ia');
    }


    public function getExcerpt(): string
    {
        return mb_strimwidth($this->body, 0, 100, "...");
    }

 
    public function tags(): array
    {

        return $this->query("SELECT t.* FROM tags t ORDER BY t.id DESC");
    }


    public function create(array $data, ?array $relations = null): bool
    {
        if (parent::create($data, $categoryId)) {
            $post_id = $this->db->getPDO()->lastInsertId();
        }

        if (isset($post_id)) {
            if (!is_null($relations)) {
                foreach ($relations as $tag_id) {
                    $stmt = $this->db->getPDO()->prepare("INSERT INTO post_tag (post_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$post_id, $tag_id]);
                }
            }
            return true;
        }

        return false;
    }


    public function update(array &$data, ?array $relations = null): bool
    {
        parent::update($data);

        $post_id = $this->id;

        print_r($relations);

        $stmt = $this->db->getPDO()->prepare("DELETE FROM post_tag WHERE post_id = ?");
        $result = $stmt->execute([$post_id]);

        if (!is_null($relations)) {
            foreach ($relations as $tag_id) {
                $stmt = $this->db->getPDO()->prepare("INSERT INTO post_tag (post_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$post_id, $tag_id]);
            }
        }

        return $result;
    }

    public function getTags(int $postId): array
    {
        return $this->query(
            "SELECT t.id, t.tag, t.created_at, case when t.id= pt.tag_id then true else false end is_selected FROM tags t
                LEFT JOIN post_tag pt ON pt.tag_id = t.id AND pt.post_id = ?
                ORDER BY t.created_at DESC",
        [$postId]);
    }

    public function getTagsOfPost(int $postId): array
    {
        return $this->query(
            "SELECT t.* FROM tags t
                JOIN post_tag pt ON pt.tag_id = t.id AND pt.post_id = ?
                ORDER BY t.created_at DESC",
        [$postId]);
    }

    public function getPostsByUserId(int $userId) {
        return $this->query(
            "SELECT * FROM posts p
                WHERE p.user_id = ?
                ORDER BY p.id DESC",
        [$userId]);
    }

    
}