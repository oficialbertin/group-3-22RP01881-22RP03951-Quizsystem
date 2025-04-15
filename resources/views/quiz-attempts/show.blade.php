@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $quizAttempt->quiz->title }}</h5>
                    <div>
                        <span class="badge bg-info" id="timer">Time Remaining: Loading...</span>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('quiz-attempts.submit', $quizAttempt) }}" method="POST" id="quizForm">
                        @csrf
                        @foreach($quizAttempt->quiz->questions as $index => $question)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Question {{ $index + 1 }}</h5>
                                    <p class="card-text">{{ $question->question_text }}</p>
                                    
                                    @if($question->type === 'multiple_choice')
                                        <div class="options">
                                            @foreach($question->options as $option)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" 
                                                        name="answers[{{ $question->id }}][option_id]" 
                                                        id="option{{ $option->id }}" 
                                                        value="{{ $option->id }}">
                                                    <label class="form-check-label" for="option{{ $option->id }}">
                                                        {{ $option->option_text }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <textarea class="form-control" 
                                                name="answers[{{ $question->id }}][answer_text]" 
                                                rows="3" 
                                                placeholder="Enter your answer here"></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to submit this quiz?')">
                                Submit Quiz
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('quizzes.index') }}'">
                                Exit Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Calculate end time
    const startTime = new Date("{{ $quizAttempt->start_time }}");
    const timeLimit = {{ $quizAttempt->quiz->time_limit ?? 0 }} * 60 * 1000; // Convert minutes to milliseconds
    const endTime = new Date(startTime.getTime() + timeLimit);

    function updateTimer() {
        const now = new Date();
        const timeLeft = endTime - now;

        if (timeLeft <= 0) {
            document.getElementById('timer').innerHTML = 'Time\'s up!';
            document.getElementById('quizForm').submit();
            return;
        }

        const minutes = Math.floor(timeLeft / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        document.getElementById('timer').innerHTML = `Time Remaining: ${minutes}m ${seconds}s`;
    }

    // Update timer every second
    setInterval(updateTimer, 1000);
    updateTimer(); // Initial call
</script>
@endsection 