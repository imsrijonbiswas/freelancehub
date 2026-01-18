<?php

$client_id = $_SESSION['client_id'];

$selected_job_id = $_GET['job_id'] ?? 0;

$jobs_query = $conn->query("
    SELECT 
        hf.job_id,
        j.job_title,
        f.full_name as freelancer_name,
        hf.status,
        (SELECT message_text FROM messages WHERE job_id = hf.job_id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages WHERE job_id = hf.job_id ORDER BY created_at DESC LIMIT 1) as last_message_time
    FROM hired_freelancers hf
    JOIN jobs j ON hf.job_id = j.job_id
    JOIN freelancers f ON hf.freelancer_id = f.freelancer_id
    WHERE hf.client_id = $client_id
    ORDER BY last_message_time DESC
");
?>

<link rel="stylesheet" href="css/messages.css">

<div class="messages-container">
    <div class="messages-sidebar">
        <h3>Conversations</h3>
        <?php if ($jobs_query && $jobs_query->num_rows > 0): ?>
            <div class="conversations-list">
                <?php while ($job = $jobs_query->fetch_assoc()): ?>
                    <a href="dashboard.php?page=messages&job_id=<?= $job['job_id'] ?>" 
                       class="conversation-item <?= $selected_job_id == $job['job_id'] ? 'active' : '' ?>">
                        <div class="conv-info">
                            <h4><?= htmlspecialchars($job['freelancer_name']) ?></h4>
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
            $job_stmt = $conn->prepare("
                SELECT j.job_title, f.full_name as freelancer_name, f.freelancer_id
                FROM jobs j
                JOIN hired_freelancers hf ON j.job_id = hf.job_id
                JOIN freelancers f ON hf.freelancer_id = f.freelancer_id
                WHERE j.job_id = ? AND hf.client_id = ?
            ");
            $job_stmt->bind_param("ii", $selected_job_id, $client_id);
            $job_stmt->execute();
            $job_info = $job_stmt->get_result()->fetch_assoc();
            
            if ($job_info):
            ?>
                <div class="chat-header">
                    <h3><?= htmlspecialchars($job_info['freelancer_name']) ?></h3>
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
                        <div class="message <?= $msg['sender_type'] == 'client' ? 'sent' : 'received' ?>">
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
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
</script>