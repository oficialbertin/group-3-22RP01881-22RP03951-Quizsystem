// Quiz System JavaScript

// Handle option selection for multiple choice questions
document.addEventListener('DOMContentLoaded', function() {
    // Option selection handling
    const optionItems = document.querySelectorAll('.option-item');
    optionItems.forEach(item => {
        item.addEventListener('click', function() {
            const questionType = this.closest('.question-card').dataset.type;
            if (questionType === 'multiple_choice') {
                // Remove selection from other options in the same question
                const questionCard = this.closest('.question-card');
                questionCard.querySelectorAll('.option-item').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
            } else if (questionType === 'true_false') {
                // Remove selection from other options in the same question
                const questionCard = this.closest('.question-card');
                questionCard.querySelectorAll('.option-item').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
            }
        });
    });

    // Handle alert dismissal
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (!alert.classList.contains('alert-persistent')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields', 'danger');
            }
        });
    });
});

// Utility Functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

// Quiz timer functionality
function startQuizTimer(duration, displayElement) {
    if (!displayElement) return;

    let timer = duration;
    const interval = setInterval(function() {
        const minutes = parseInt(timer / 60, 10);
        const seconds = parseInt(timer % 60, 10);

        displayElement.textContent = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

        if (--timer < 0) {
            clearInterval(interval);
            document.querySelector('form').submit();
        }
    }, 1000);
}

// Confirmation dialogs
function confirmDelete(event) {
    if (!confirm('Are you sure you want to delete this item?')) {
        event.preventDefault();
    }
}

// Dynamic form fields
function addOptionField() {
    const optionsContainer = document.getElementById('options-container');
    const optionCount = optionsContainer.children.length;
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'form-group';
    optionDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <input type="text" 
                   name="options[]" 
                   class="form-control" 
                   placeholder="Option ${optionCount + 1}"
                   required>
            <button type="button" 
                    class="btn btn-danger ml-2" 
                    onclick="this.parentElement.remove()">
                Remove
            </button>
        </div>
    `;
    
    optionsContainer.appendChild(optionDiv);
} 