<?php
// FreelanceHub/freelancer/pages/messages.php

$freelancer_id = $_SESSION['freelancer_id'];

// Get job ID from query if viewing a conversation
$selected_job_id = $_GET['job_id'] ?? 0;

// Get all hired jobs with last message
$jobs_query = $conn->query("
    SELECT 
        hf.job_id,
        j.job_title,
        c.full_name as client_name,
        hf.status,
        (SELECT message_text FROM messages WHERE job_id = hf.job_id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages WHERE job_id = hf.job_id ORDER BY created_at DESC LIMIT 1) as last_message_time
    FROM hired_freelancers hf
    JOIN jobs j ON hf.job_id = j.job_id
    JOIN clients c ON hf.client_id = c.client_id
    WHERE hf.freelancer_id = $freelancer_id
    ORDER BY last_message_time DESC
");
?>

<div class="messages-container">
    <div class="messages-sidebar">
        <h3>Conversations</h3>
        <?php if ($jobs_query && $jobs_query->num_rows > 0): ?>
            <div class="conversations-list">
                <?php while ($job = $jobs_query->fetch_assoc()): ?>
                    <a href="dashboard.php?page=messages&job_id=<?= $job['job_id'] ?>" 
                       class="conversation-item <?= $selected_job_id == $job['job_id'] ? 'active' : '' ?>">
                        <div class="conv-info">
                            <h4><?= htmlspecialchars($job['client_name']) ?></h4>
                            <p class="conv-job"><?= htmlspecialchars($job['job_title']) ?></p>
                            <?php if ($job['last_message']): ?>
                                <p class="conv-preview"><?= htmlspecialchars(substr($job['last_message'], 0, 50)) ?>...</p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-conversations">No conversations yet</p>
        <?php endif; ?>
    </div>
    
    <div class="messages-main">
        <?php if ($selected_job_id > 0): ?>
            <?php
            // Get job details
            $job_stmt = $conn->prepare("
                SELECT j.job_title, c.full_name as client_name, c.client_id
                FROM jobs j
                JOIN hired_freelancers hf ON j.job_id = hf.job_id
                JOIN clients c ON j.client_id = c.client_id
                WHERE j.job_id = ? AND hf.freelancer_id = ?
            ");
            $job_stmt->bind_param("ii", $selected_job_id, $freelancer_id);
            $job_stmt->execute();
            $job_info = $job_stmt->get_result()->fetch_assoc();
            
            if ($job_info):
            ?>
                <div class="chat-header">
                    <h3><?= htmlspecialchars($job_info['client_name']) ?></h3>
                    <p><?= htmlspecialchars($job_info['job_title']) ?></p>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <?php
                    $messages_query = $conn->prepare("
                        SELECT message_text, sender_type, created_at
                        FROM messages
                        WHERE job_id = ?
                        ORDER BY created_at ASC
                    ");
                    $messages_query->bind_param("i", $selected_job_id);
                    $messages_query->execute();
                    $messages = $messages_query->get_result();
                    
                    while ($msg = $messages->fetch_assoc()):
                    ?>
                        <div class="message <?= $msg['sender_type'] == 'freelancer' ? 'sent' : 'received' ?>">
                            <div class="message-bubble">
                                <p><?= nl2br(htmlspecialchars($msg['message_text'])) ?></p>
                                <span class="message-time"><?= date('h:i A', strtotime($msg['created_at'])) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <form class="chat-input-form" method="POST" action="actions/send_message.php">
                    <input type="hidden" name="job_id" value="<?= $selected_job_id ?>">
                    <input type="text" name="message" placeholder="Type your message..." required>
                    <button type="submit">Send</button>
                </form>
            <?php else: ?>
                <div class="empty-chat">
                    <p>Conversation not found</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-chat">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                    <path d="M20 24h24v16H28l-8 8v-8z" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <p>Select a conversation to start messaging</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-scroll to bottom
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
</script>

<style>
.messages-container {
    display: flex;
    gap: 20px;
    height: calc(100vh - 200px);
}

.messages-sidebar {
    width: 300px;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow-y: auto;
}

.messages-sidebar h3 {
    margin: 0 0 20px 0;
    font-size: 1.2rem;
    color: #1a1a1a;
}

.conversations-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.conversation-item {
    padding: 15px;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.2s ease;
    border: 1px solid #f0f0f0;
}

.conversation-item:hover {
    background: #f8f9fa;
}

.conversation-item.active {
    background: #e3f2fd;
    border-color: #00a2ff;
}

.conv-info h4 {
    margin: 0 0 5px 0;
    font-size: 0.95rem;
    color: #1a1a1a;
}

.conv-job {
    font-size: 0.85rem;
    color: #666;
    margin: 0 0 5px 0;
}

.conv-preview {
    font-size: 0.8rem;
    color: #999;
    margin: 0;
}

.no-conversations {
    text-align: center;
    color: #999;
    padding: 20px;
}

.messages-main {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.chat-header h3 {
    margin: 0 0 5px 0;
    font-size: 1.2rem;
    color: #1a1a1a;
}

.chat-header p {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message {
    display: flex;
}

.message.sent {
    justify-content: flex-end;
}

.message.received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 60%;
    padding: 12px 16px;
    border-radius: 12px;
}

.message.sent .message-bubble {
    background: #00a2ff;
    color: #fff;
}

.message.received .message-bubble {
    background: #f0f0f0;
    color: #333;
}

.message-bubble p {
    margin: 0 0 5px 0;
    font-size: 0.95rem;
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.7;
}

.chat-input-form {
    display: flex;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid #f0f0f0;
}

.chat-input-form input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
}

.chat-input-form button {
    padding: 12px 30px;
    background: #00a2ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

.chat-input-form button:hover {
    background: #0090e0;
}

.empty-chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
}

@media (max-width: 768px) {
    .messages-container {
        flex-direction: column;
    }
    
    .messages-sidebar {
        width: 100%;
        height: 200px;
    }
}
</style>