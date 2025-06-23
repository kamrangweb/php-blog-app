class ActivityPool {
    constructor() {
        this.eventSource = null;
        this.notificationContainer = null;
        this.wordCountDisplay = null;
        this.isConnected = false;
        this.currentWordCount = 0;
        this.lastNotificationLevel = 'normal';
        
        this.init();
    }

    init() {
        this.createNotificationContainer();
        this.createWordCountDisplay();
        this.connectToActivityStream();
        this.setupContentTracking();
    }

    createNotificationContainer() {
        // Create notification container
        this.notificationContainer = document.createElement('div');
        this.notificationContainer.id = 'activity-notifications';
        this.notificationContainer.className = 'activity-notifications';
        this.notificationContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            max-height: 80vh;
            overflow-y: auto;
        `;
        document.body.appendChild(this.notificationContainer);
    }

    createWordCountDisplay() {
        // Create word count display
        this.wordCountDisplay = document.createElement('div');
        this.wordCountDisplay.id = 'word-count-display';
        this.wordCountDisplay.className = 'word-count-display';
        this.wordCountDisplay.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #fff;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 10px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9998;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            transition: all 0.3s ease;
        `;
        document.body.appendChild(this.wordCountDisplay);
    }

    connectToActivityStream() {
        if (this.eventSource) {
            this.eventSource.close();
        }

        this.eventSource = new EventSource('/api/activity/subscribe');
        
        this.eventSource.onopen = () => {
            this.isConnected = true;
            console.log('Connected to Activity Pool stream');
        };

        this.eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                this.handleNotification(data);
            } catch (error) {
                console.error('Error parsing notification:', error);
            }
        };

        this.eventSource.onerror = (error) => {
            console.error('EventSource error:', error);
            this.isConnected = false;
            // Try to reconnect after 5 seconds
            setTimeout(() => this.connectToActivityStream(), 5000);
        };
    }

    setupContentTracking() {
        const contentTextarea = document.getElementById('input_content');
        if (!contentTextarea) return;

        // Track content changes
        contentTextarea.addEventListener('input', (e) => {
            this.trackContent(e.target.value);
        });

        // Track content on paste
        contentTextarea.addEventListener('paste', (e) => {
            setTimeout(() => {
                this.trackContent(e.target.value);
            }, 100);
        });

        // Initial tracking
        this.trackContent(contentTextarea.value);
    }

    trackContent(content) {
        const wordCount = this.countWords(content);
        this.currentWordCount = wordCount;
        
        this.updateWordCountDisplay(wordCount);
        this.sendToActivityPool(content, wordCount);
    }

    countWords(text) {
        if (!text || text.trim() === '') return 0;
        
        // Remove HTML tags and count words
        const cleanText = text.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        return cleanText.split(' ').filter(word => word.length > 0).length;
    }

    updateWordCountDisplay(wordCount) {
        let color = '#007bff';
        let borderColor = '#007bff';
        
        if (wordCount >= 2000) {
            color = '#dc3545';
            borderColor = '#dc3545';
        } else if (wordCount >= 500) {
            color = '#28a745';
            borderColor = '#28a745';
        }

        this.wordCountDisplay.style.color = color;
        this.wordCountDisplay.style.borderColor = borderColor;
        this.wordCountDisplay.textContent = `${wordCount} words`;
    }

    async sendToActivityPool(content, wordCount) {
        try {
            const response = await fetch('/api/activity/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    content: content,
                    word_count: wordCount,
                    post_id: this.getPostId()
                })
            });

            if (!response.ok) {
                throw new Error('Failed to track content');
            }

            const result = await response.json();
            console.log('Content tracked:', result);
        } catch (error) {
            console.error('Error tracking content:', error);
        }
    }

    getPostId() {
        // Try to get post ID from URL or form
        const urlParams = new URLSearchParams(window.location.search);
        const pathParts = window.location.pathname.split('/');
        const editIndex = pathParts.indexOf('edit');
        
        if (editIndex !== -1 && pathParts[editIndex + 1]) {
            return pathParts[editIndex + 1];
        }
        
        return null;
    }

    handleNotification(data) {
        if (data.type === 'connected') {
            console.log('Connected to activity pool');
            return;
        }

        if (data.error) {
            console.error('Activity pool error:', data.error);
            return;
        }

        this.showNotification(data);
    }

    showNotification(notification) {
        const notificationElement = document.createElement('div');
        notificationElement.className = `activity-notification notification-${notification.level}`;
        notificationElement.style.cssText = `
            background: ${this.getNotificationColor(notification.level)};
            color: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
            position: relative;
            overflow: hidden;
        `;

        // Add pulsing effect for warnings
        if (notification.level === 'warning') {
            notificationElement.style.animation += ', pulse 2s infinite';
        }

        notificationElement.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="flex: 1;">
                    <div style="font-weight: bold; margin-bottom: 5px;">
                        ${this.getNotificationIcon(notification.level)} ${notification.level.toUpperCase()}
                    </div>
                    <div style="font-size: 14px; line-height: 1.4;">
                        ${notification.message}
                    </div>
                    <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                        ${new Date(notification.timestamp * 1000).toLocaleTimeString()}
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; margin-left: 10px;">
                    ×
                </button>
            </div>
        `;

        this.notificationContainer.appendChild(notificationElement);

        // Auto-remove after 10 seconds (except warnings)
        if (notification.level !== 'warning') {
            setTimeout(() => {
                if (notificationElement.parentNode) {
                    notificationElement.remove();
                }
            }, 10000);
        }
    }

    getNotificationColor(level) {
        switch (level) {
            case 'warning':
                return '#dc3545';
            case 'info':
                return '#17a2b8';
            default:
                return '#6c757d';
        }
    }

    getNotificationIcon(level) {
        switch (level) {
            case 'warning':
                return '⚠️';
            case 'info':
                return 'ℹ️';
            default:
                return '📝';
        }
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.isConnected = false;
        }
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        50% {
            box-shadow: 0 4px 20px rgba(220, 53, 69, 0.6);
        }
        100% {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
    }
    
    .word-count-display:hover {
        transform: scale(1.05);
    }
`;
document.head.appendChild(style);

// Initialize Activity Pool when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on blog post creation/editing pages
    if (window.location.pathname.includes('/admin/posts/create') || 
        window.location.pathname.includes('/admin/posts/edit')) {
        window.activityPool = new ActivityPool();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.activityPool) {
        window.activityPool.disconnect();
    }
}); 