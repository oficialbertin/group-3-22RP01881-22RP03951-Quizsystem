@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $quiz->title }}</h5>
                    @if(Auth::user()->isLecturer() && $quiz->user_id === Auth::id())
                        <div class="btn-group">
                            <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-primary btn-sm">Edit Quiz</a>
                            <a href="{{ route('quizzes.questions.create', $quiz) }}" class="btn btn-success btn-sm">Add Question</a>
                            @if(!$quiz->is_published)
                                <form action="{{ route('quizzes.publish', $quiz) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">Publish Quiz</button>
                                </form>
                            @else
                                <form action="{{ route('quizzes.unpublish', $quiz) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">Unpublish Quiz</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="quiz-details mb-4">
                        <h6>Quiz Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Description:</strong> {{ $quiz->description ?? 'No description provided' }}</p>
                                <p><strong>Total Questions:</strong> {{ $quiz->questions->count() }}</p>
                                <p><strong>Total Marks:</strong> {{ $quiz->total_marks }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Time Limit:</strong> {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No time limit' }}</p>
                                <p><strong>Start Time:</strong> {{ $quiz->start_time ? $quiz->start_time->format('Y-m-d H:i:s') : 'No start time set' }}</p>
                                <p><strong>End Time:</strong> {{ $quiz->end_time ? $quiz->end_time->format('Y-m-d H:i:s') : 'No end time set' }}</p>
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->isLecturer() && $quiz->user_id === Auth::id())
                        <!-- Lecturer View - Show all questions -->
                        <div class="questions-section">
                            <h6 class="mb-3">Questions</h6>
                            @forelse($quiz->questions as $question)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="card-title">{{ $loop->iteration }}. {{ $question->text }}</h6>
                                            <div class="btn-group">
                                                <a href="{{ route('quizzes.questions.edit', [$quiz, $question]) }}" class="btn btn-primary btn-sm">Edit</a>
                                                <form action="{{ route('quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                                            <div class="options mt-3">
                                                @foreach($question->options as $option)
                                                    <div class="option @if($option->is_correct) correct-answer @endif">
                                                        {{ $option->text }}
                                                        @if($option->is_correct)
                                                            <span class="badge bg-success">Correct Answer</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="mt-3">
                                                <em>Short Answer Question</em>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    No questions added to this quiz yet.
                                </div>
                            @endforelse
                        </div>
                    @else
                        <!-- Student View - Show quiz attempt button or status -->
                        <div class="text-center mt-4">
                            @php
                                $attempt = $quiz->attempts()->where('user_id', Auth::id())->latest()->first();
                            @endphp

                            @if(!$attempt)
                                @if($quiz->is_published)
                                    <form action="{{ route('quiz-attempts.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                                        <button type="submit" class="btn btn-primary">Start Quiz</button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        This quiz is not yet available.
                                    </div>
                                @endif
                            @elseif($attempt->completed_at)
                                <div class="alert alert-info">
                                    You have completed this quiz. 
                                    <a href="{{ route('results.show', $attempt) }}" class="btn btn-link">View Your Results</a>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    You have an incomplete attempt.
                                    <a href="{{ route('quiz-attempts.show', $attempt) }}" class="btn btn-warning">Continue Quiz</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 