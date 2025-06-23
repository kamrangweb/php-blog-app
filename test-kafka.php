<?php

echo "🧪 Kafka Connection Test\n";
echo "=======================\n\n";

// Check if rdkafka extension is loaded
echo "1. Checking rdkafka extension...\n";
if (extension_loaded('rdkafka')) {
    echo "✅ rdkafka extension is loaded successfully!\n";
} else {
    echo "❌ rdkafka extension is not loaded.\n";
    echo "   Please follow the installation guide in RDKAFKA_INSTALLATION_GUIDE.md\n\n";
    exit(1);
}

// Check if Kafka is running
echo "\n2. Checking Kafka connection...\n";
try {
    $conf = new RdKafka\Conf();
    $conf->set('metadata.broker.list', 'localhost:9092');
    $producer = new RdKafka\Producer($conf);
    
    echo "✅ Successfully connected to Kafka!\n";
    
    // Test topic creation
    echo "\n3. Testing topic creation...\n";
    $topic = $producer->newTopic('blog_activity_pool');
    
    // Send a test message
    $testMessage = [
        'type' => 'test_message',
        'timestamp' => time(),
        'message' => 'Hello from PHP!',
        'test' => true
    ];
    
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($testMessage));
    $producer->flush(1000);
    
    echo "✅ Test message sent successfully!\n";
    echo "   Check Kafka UI at http://localhost:8080 to see the message\n";
    
} catch (Exception $e) {
    echo "❌ Failed to connect to Kafka: " . $e->getMessage() . "\n";
    echo "   Make sure Kafka is running: .\\docker-setup.bat status\n";
    exit(1);
}

echo "\n🎉 All tests passed! Your Kafka setup is working correctly.\n";
echo "\n📊 Next steps:\n";
echo "   1. Start your blog application\n";
echo "   2. Navigate to /admin/posts/create\n";
echo "   3. Start typing to see real-time activity tracking\n";
echo "   4. Check Kafka UI at http://localhost:8080 for messages\n";

?> 