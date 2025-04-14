@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h2>{{ $attempt->quiz->title }} - Results</h2>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Quiz Information</h4>
                    <p>
                        <strong>Student:</strong> {{ $attempt->user->name }}<br>
                        <strong>Score:</strong> {{ $attempt->score }}/{{ $attempt->total_questions }}<br>
                        <strong>Percentage:</strong> {{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%<br>
                        <strong>Time Taken:</strong> {{ $attempt->completed_at->diffForHumans($attempt->started_at) }}<br>
                        <strong>Completed At:</strong> {{ $attempt->completed_at->format('Y-m-d H:i:s') }}
                    </p>
                </div>
            </div>

            <h4>Questions and Answers</h4>
            @foreach($attempt->answers as $answer)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Question {{ $loop->iteration }}</h5>
                        <p class="card-text">{{ $answer->question->text }}</p>

                        @if($answer->question->type === 'multiple_choice' || $answer->question->type === 'true_false')
                            <div class="options">
                                @foreach($answer->question->options as $option)
                                    <div class="option @if($option->id === $answer->selected_option_id) selected @endif @if($option->is_correct) correct @endif">
                                        {{ $option->text }}
                                        @if($option->id === $answer->selected_option_id && $option->is_correct)
                                            <span class="badge bg-success">Correct Answer</span>
                                        @elseif($option->id === $answer->selected_option_id && !$option->is_correct)
                                            <span class="badge bg-danger">Your Answer</span>
                                        @elseif($option->is_correct)
                                            <span class="badge bg-success">Correct Answer</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="short-answer">
                                <p><strong>Your Answer:</strong></p>
                                <p>{{ $answer->text_answer }}</p>
                                @if(auth()->user()->isLecturer())
                                    <div class="mt-3">
                                        <form action="{{ route('answers.grade', $answer->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="score">Score (out of 1):</label>
                                                <input type="number" name="score" id="score" class="form-control" min="0" max="1" step="0.1" value="{{ $answer->score ?? 0 }}">
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="feedback">Feedback:</label>
                                                <textarea name="feedback" id="feedback" class="form-control" rows="2">{{ $answer->feedback }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary mt-2">Save Grade</button>
                                        </form>
                                    </div>
                                @else
                                    @if($answer->feedback)
                                        <div class="mt-3">
                                            <p><strong>Score:</strong> {{ $answer->score }}/1</p>
                                            <p><strong>Feedback:</strong> {{ $answer->feedback }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="text-center mb-4">
        <a href="{{ route('results.index') }}" class="btn btn-secondary">Back to Results</a>
    </div>
</div>
@endsection 