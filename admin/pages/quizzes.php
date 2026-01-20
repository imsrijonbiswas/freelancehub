<?php
// FreelanceHub/admin/pages/quizzes.php

$skills_query = $conn->query("SELECT skill_id, skill_name FROM skill_categories ORDER BY skill_name");

$filter_skill = isset($_GET['skill']) ? intval($_GET['skill']) : 0;

if ($filter_skill > 0) {
    $questions_query = $conn->query("
        SELECT q.question_id, q.question_text, q.created_at, s.skill_name, s.skill_id
        FROM questions q
        JOIN skill_categories s ON q.skill_id = s.skill_id
        WHERE q.skill_id = $filter_skill
        ORDER BY q.created_at DESC
    ");
} else {
    $questions_query = $conn->query("
        SELECT q.question_id, q.question_text, q.created_at, s.skill_name, s.skill_id
        FROM questions q
        JOIN skill_categories s ON q.skill_id = s.skill_id
        ORDER BY q.created_at DESC
    ");
}

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php if ($message == 'added'): ?>
    <div class="success-message">Question added successfully!</div>
<?php elseif ($message == 'deleted'): ?>
    <div class="success-message">Question deleted successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="quiz-section">
    <div class="section-header">
        <h2>Add New Question</h2>
    </div>
    
    <form action="actions/add_question.php" method="POST" class="quiz-form">
        <div class="form-row">
            <div class="form-group">
                <label for="skill_id">Skill Category *</label>
                <select name="skill_id" id="skill_id" required>
                    <option value="">Select Skill</option>
                    <?php 
                    $skills_query->data_seek(0); // Reset pointer
                    while ($skill = $skills_query->fetch_assoc()): 
                    ?>
                        <option value="<?= $skill['skill_id'] ?>"><?= htmlspecialchars($skill['skill_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="question_text">Question *</label>
            <textarea name="question_text" id="question_text" rows="3" required 
                placeholder="Enter your question here..."></textarea>
        </div>
        
        <div class="options-grid">
            <div class="option-box">
                <label>Option A *</label>
                <input type="text" name="option_a" required placeholder="Enter option A">
                <label class="radio-label">
                    <input type="radio" name="correct_answer" value="A" required>
                    <span>Correct Answer</span>
                </label>
            </div>
            
            <div class="option-box">
                <label>Option B *</label>
                <input type="text" name="option_b" required placeholder="Enter option B">
                <label class="radio-label">
                    <input type="radio" name="correct_answer" value="B" required>
                    <span>Correct Answer</span>
                </label>
            </div>
            
            <div class="option-box">
                <label>Option C *</label>
                <input type="text" name="option_c" required placeholder="Enter option C">
                <label class="radio-label">
                    <input type="radio" name="correct_answer" value="C" required>
                    <span>Correct Answer</span>
                </label>
            </div>
            
            <div class="option-box">
                <label>Option D *</label>
                <input type="text" name="option_d" required placeholder="Enter option D">
                <label class="radio-label">
                    <input type="radio" name="correct_answer" value="D" required>
                    <span>Correct Answer</span>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn-primary">Add Question</button>
    </form>
</div>

<div class="quiz-section">
    <div class="section-header">
        <h2>All Questions</h2>
        <form method="GET" action="dashboard.php" class="filter-form">
            <input type="hidden" name="page" value="quizzes">
            <select name="skill" onchange="this.form.submit()">
                <option value="0">All Skills</option>
                <?php 
                $skills_query->data_seek(0);
                while ($skill = $skills_query->fetch_assoc()): 
                ?>
                    <option value="<?= $skill['skill_id'] ?>" <?= $filter_skill == $skill['skill_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($skill['skill_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
    
    <?php if ($questions_query && $questions_query->num_rows > 0): ?>
        <div class="questions-list">
            <?php while ($question = $questions_query->fetch_assoc()): ?>
                <?php
                $qid = $question['question_id'];
                $options_query = $conn->query("
                    SELECT option_label, option_text, is_correct 
                    FROM question_options 
                    WHERE question_id = $qid 
                    ORDER BY option_label
                ");
                ?>
                
                <div class="question-card">
                    <div class="question-header">
                        <span class="skill-badge"><?= htmlspecialchars($question['skill_name']) ?></span>
                        <span class="question-date"><?= date('M d, Y', strtotime($question['created_at'])) ?></span>
                    </div>
                    
                    <div class="question-content">
                        <p class="question-text-display"><?= htmlspecialchars($question['question_text']) ?></p>
                        
                        <div class="options-display">
                            <?php while ($option = $options_query->fetch_assoc()): ?>
                                <div class="option-display <?= $option['is_correct'] ? 'correct-option' : '' ?>">
                                    <span class="option-label"><?= $option['option_label'] ?>.</span>
                                    <span class="option-text-display"><?= htmlspecialchars($option['option_text']) ?></span>
                                    <?php if ($option['is_correct']): ?>
                                        <span class="correct-badge">✓ Correct</span>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <div class="question-actions">
                        <a href="actions/delete_question.php?id=<?= $question['question_id'] ?>" 
                           class="btn-danger btn-small"
                           onclick="return confirm('Are you sure you want to delete this question?')">
                            Delete
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                <path d="M32 20v24M20 32h24" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <p>No questions found. Add your first question above!</p>
        </div>
    <?php endif; ?>
</div>