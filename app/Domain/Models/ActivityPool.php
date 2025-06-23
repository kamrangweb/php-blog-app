<?php

namespace App\Domain\Models;

use Kafka\Producer;
use Kafka\ProducerConfig;
use Kafka\Consumer;
use Kafka\ConsumerConfig;

class ActivityPool
{
    private $topic = 'blog_activity_pool';
    private $broker = 'localhost:9092';

    public function trackContentVolume($userId, $postId, $content, $wordCount)
    {
        $message = [
            'type' => 'content_volume_track',
            'user_id' => $userId,
            'post_id' => $postId,
            'word_count' => $wordCount,
            'content_length' => strlen($content),
            'timestamp' => time(),
            'notification_level' => $this->getNotificationLevel($wordCount)
        ];

        $config = ProducerConfig::getInstance();
        $config->setMetadataBrokerList($this->broker);
        $producer = new Producer();
        $producer->send([
            [
                'topic' => $this->topic,
                'value' => json_encode($message),
                'key' => (string)$userId
            ]
        ]);

        return $message;
    }

    private function getNotificationLevel($wordCount)
    {
        if ($wordCount >= 2000) {
            return 'warning';
        } elseif ($wordCount >= 500) {
            return 'info';
        } else {
            return 'normal';
        }
    }

    public function getNotifications($userId, $timeout = 1000)
    {
        $config = ConsumerConfig::getInstance();
        $config->setMetadataBrokerList($this->broker);
        $config->setGroupId('blog_activity_pool_group');
        $config->setBrokerVersion('1.0.0');
        $config->setTopics([$this->topic]);
        $config->setOffsetReset('latest');

        $consumer = new Consumer();
        $notifications = [];
        $consumer->start(function($topic, $part, $message) use ($userId, &$notifications) {
            $data = json_decode($message['message']['value'], true);
            if (isset($data['user_id']) && $data['user_id'] == $userId) {
                $notifications[] = $this->formatNotification($data);
            }
        });
        return $notifications;
    }

    private function formatNotification($data)
    {
        return [
            'id' => uniqid(),
            'type' => $data['type'],
            'message' => $this->getNotificationMessage($data['word_count'], $data['notification_level']),
            'level' => $data['notification_level'],
            'word_count' => $data['word_count'],
            'timestamp' => $data['timestamp']
        ];
    }

    private function getNotificationMessage($wordCount, $level)
    {
        switch ($level) {
            case 'warning':
                return "⚠️ WARNING: Your blog post has reached {$wordCount} words! This is quite lengthy. Consider breaking it into multiple posts.";
            case 'info':
                return "ℹ️ INFO: Your blog post has reached {$wordCount} words. Good content length!";
            default:
                return "📝 Your blog post currently has {$wordCount} words.";
        }
    }
} 