@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Quiz Results: {{ $quizAttempt->quiz->title }}</h3>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Student:</strong> {{ $quizAttempt->user->name }}</p>
                            <p><strong>Score:</strong> {{ $quizAttempt->score ?? 0 }} / {{ $quizAttempt->quiz->total_marks }}</p>
                            <p><strong>Percentage:</strong> {{ $quizAttempt->score ? number_format(($quizAttempt->score / $quizAttempt->quiz->total_marks) * 100, 1) : 0 }}%</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Started At:</strong> {{ $quizAttempt->start_time ? $quizAttempt->start_time->format('M d, Y H:i') : 'Not started' }}</p>
                            <p><strong>Completed At:</strong> {{ $quizAttempt->end_time ? $quizAttempt->end_time->format('M d, Y H:i') : 'In progress' }}</p>
                            @if($quizAttempt->start_time && $quizAttempt->end_time)
                                <p><strong>Duration:</strong> {{ $quizAttempt->start_time->diffInMinutes($quizAttempt->end_time) }} minutes</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <h4 class="mb-3">Question Details</h4>
                    @forelse($quizAttempt->answers as $answer)
                        <div class="card mb-3 {{ $answer->is_correct ? 'border-success' : 'border-danger' }}">
                            <div class="card-body">
                                <h5 class="card-title">Question {{ $loop->iteration }}</h5>
                                <p class="card-text">{{ $answer->question->question_text }}</p>
                                
                                <div class="mt-3">
                                    <p><strong>Your Answer:</strong></p>
                                    @if($answer->question->type === 'multiple_choice')
                                        <p>{{ $answer->option->option_text ?? 'Not answered' }}</p>
                                    @else
                                        <p>{{ $answer->answer_text ?? 'Not answered' }}</p>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    @if($answer->is_correct)
                                        <span class="badge bg-success">Correct</span>
                                    @else
                                        <span class="badge bg-danger">Incorrect</span>
                                    @endif
                                    <span class="ms-2">Marks: {{ $answer->marks_obtained ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            No answers submitted yet.
                        </div>
                    @endforelse

                    <div class="mt-4">
                        <a href="{{ route('results.index') }}" class="btn btn-secondary">
                            Back to Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@endsection 