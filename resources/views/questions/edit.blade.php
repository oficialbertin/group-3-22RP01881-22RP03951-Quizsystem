@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Question - {{ $quiz->title }}</h5>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('quizzes.questions.update', ['quiz' => $quiz->id, 'question' => $question->id]) }}" method="POST" id="questionForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="text" class="form-label">Question Text</label>
                            <textarea name="text" id="text" class="form-control @error('text') is-invalid @enderror" rows="3" required>{{ old('text', $question->question_text) }}</textarea>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Question Type</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select a type</option>
                                <option value="multiple_choice" {{ old('type', $question->question_type) === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="true_false" {{ old('type', $question->question_type) === 'true_false' ? 'selected' : '' }}>True/False</option>
                                <option value="short_answer" {{ old('type', $question->question_type) === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="optionsSection" class="mb-3" style="display: none;">
                            <div id="multipleChoiceOptions" style="display: none;">
                                <h6 class="mb-3">Options (Select the correct answer)</h6>
                                <div id="optionsList">
                                    @if($question->question_type === 'multiple_choice')
                                        @foreach($question->options as $index => $option)
                                            <div class="option-item mb-3">
                                                <div class="input-group">
                                                    <input type="text" name="options[]" class="form-control" placeholder="Option text" value="{{ $option->option_text }}" required>
                                                    <div class="input-group-text">
                                                        <input type="radio" name="correct_option" value="{{ $index }}" class="form-check-input mt-0" {{ $option->is_correct ? 'checked' : '' }} required>
                                                        <span class="ms-2">Correct</span>
                                                    </div>
                                                    <button type="button" class="btn btn-danger remove-option" onclick="removeOption(this)">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addOption()">Add Option</button>
                            </div>

                            <div id="trueFalseOptions" style="display: none;">
                                <h6 class="mb-3">Select Correct Answer</h6>
                                <div class="form-check mb-2">
                                    <input type="radio" name="true_false_answer" value="1" class="form-check-input" 
                                        {{ $question->question_type === 'true_false' && $question->options->where('option_text', 'True')->first()->is_correct ? 'checked' : '' }}>
                                    <label class="form-check-label">True</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="true_false_answer" value="0" class="form-check-input"
                                        {{ $question->question_type === 'true_false' && $question->options->where('option_text', 'False')->first()->is_correct ? 'checked' : '' }}>
                                    <label class="form-check-label">False</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="marks" class="form-label">Marks</label>
                            <input type="number" name="marks" id="marks" class="form-control @error('marks') is-invalid @enderror" value="{{ old('marks', $question->marks) }}" min="1" required>
                            @error('marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('quizzes.questions.index', $quiz) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Function to handle question type changes
function handleTypeChange() {
    const typeSelect = document.getElementById('type');
    const optionsSection = document.getElementById('optionsSection');
    const multipleChoiceOptions = document.getElementById('multipleChoiceOptions');
    const trueFalseOptions = document.getElementById('trueFalseOptions');
    const optionsList = document.getElementById('optionsList');

    // Hide all sections first
    optionsSection.style.display = 'none';
    multipleChoiceOptions.style.display = 'none';
    trueFalseOptions.style.display = 'none';

    // Show relevant section based on selected type
    if (typeSelect.value === 'multiple_choice') {
        optionsSection.style.display = 'block';
        multipleChoiceOptions.style.display = 'block';
        // Add initial options if none exist
        if (optionsList.children.length === 0) {
            addOption();
            addOption();
        }
    } else if (typeSelect.value === 'true_false') {
        optionsSection.style.display = 'block';
        trueFalseOptions.style.display = 'block';
    }
}

// Function to add a new option
function addOption() {
    const optionsList = document.getElementById('optionsList');
    const optionCount = optionsList.children.length;
    
    const optionItem = document.createElement('div');
    optionItem.className = 'option-item mb-3';
    optionItem.innerHTML = `
        <div class="input-group">
            <input type="text" name="options[]" class="form-control" placeholder="Option text" required>
            <div class="input-group-text">
                <input type="radio" name="correct_option" value="${optionCount}" class="form-check-input mt-0" required>
                <span class="ms-2">Correct</span>
            </div>
            <button type="button" class="btn btn-danger remove-option" onclick="removeOption(this)">Remove</button>
        </div>
    `;
    
    optionsList.appendChild(optionItem);
    updateRemoveButtons();
}

// Function to remove an option
function removeOption(button) {
    const optionsList = document.getElementById('optionsList');
    if (optionsList.children.length > 2) {
        const optionItem = button.closest('.option-item');
        optionItem.remove();
        
        // Update the correct_option values
        const options = document.querySelectorAll('input[name="correct_option"]');
        options.forEach((option, index) => {
            option.value = index;
        });
        
        updateRemoveButtons();
    }
}

// Function to update remove buttons state
function updateRemoveButtons() {
    const optionsList = document.getElementById('optionsList');
    const removeButtons = optionsList.querySelectorAll('.remove-option');
    
    removeButtons.forEach(button => {
        button.disabled = optionsList.children.length <= 2;
    });
}

// Function to validate form before submission
function validateForm(e) {
    e.preventDefault();
    console.log('Form validation started');
    
    const type = document.getElementById('type').value;
    const text = document.getElementById('text').value;
    const marks = document.getElementById('marks').value;
    
    console.log('Form values:', { type, text, marks });
    
    // Basic validation
    if (!text || !type || !marks) {
        alert('Please fill in all required fields');
        return false;
    }
    
    // Type-specific validation
    if (type === 'multiple_choice') {
        const options = document.querySelectorAll('input[name="options[]"]');
        const correctOption = document.querySelector('input[name="correct_option"]:checked');
        
        console.log('Multiple choice validation:', {
            optionsCount: options.length,
            hasCorrectOption: !!correctOption
        });
        
        if (options.length < 2) {
            alert('Please add at least 2 options for multiple choice questions');
            return false;
        }
        
        let allFilled = true;
        options.forEach(option => {
            if (!option.value.trim()) {
                allFilled = false;
            }
        });
        
        if (!allFilled) {
            alert('Please fill in all option fields');
            return false;
        }
        
        if (!correctOption) {
            alert('Please select the correct answer');
            return false;
        }
    } else if (type === 'true_false') {
        const trueFalseAnswer = document.querySelector('input[name="true_false_answer"]:checked');
        console.log('True/False validation:', { hasAnswer: !!trueFalseAnswer });
        
        if (!trueFalseAnswer) {
            alert('Please select the correct answer for True/False question');
            return false;
        }
    }
    
    console.log('Validation passed, submitting form');
    const form = document.getElementById('questionForm');
    const formData = new FormData(form);
    
    // Log form data
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    form.submit();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing form');
    
    // Check if a type is already selected
    const selectedType = document.getElementById('type').value;
    console.log('Initial question type:', selectedType);
    
    handleTypeChange();
    
    // Add event listeners
    document.getElementById('type').addEventListener('change', handleTypeChange);
    document.getElementById('questionForm').addEventListener('submit', validateForm);
    
    console.log('Form initialization complete');
});
</script>
@endsection 