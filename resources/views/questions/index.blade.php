@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Questions for: {{ $quiz->title }}</h5>
                    <a href="{{ route('quizzes.questions.create', $quiz) }}" class="btn btn-primary">Add New Question</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @forelse($questions as $question)
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
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

                                <p><strong>Type:</strong> {{ ucfirst($question->type) }}</p>

                                @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                                    <div class="options">
                                        <p><strong>Options:</strong></p>
                                        <ul class="list-group">
                                            @foreach($question->options as $option)
                                                <li class="list-group-item @if($option->is_correct) list-group-item-success @endif">
                                                    {{ $option->text }}
                                                    @if($option->is_correct)
                                                        <span class="badge bg-success float-end">Correct Answer</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><em>This is a short answer question.</em></p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            No questions added to this quiz yet. Click the "Add New Question" button to create one.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-secondary">Back to Quiz</a>
            </div>
        </div>
    </div>
</div>
@endsection 