@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Quizzes') }}</h5>
                    @if(Auth::user()->isLecturer())
                        <a href="{{ route('quizzes.create') }}" class="btn btn-primary">Create New Quiz</a>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(Auth::user()->isLecturer())
                        <!-- Lecturer View -->
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Duration (minutes)</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quizzes as $quiz)
                                        <tr>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ Str::limit($quiz->description, 50) }}</td>
                                            <td>{{ $quiz->duration }}</td>
                                            <td>
                                                @if($quiz->is_published)
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-warning">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('quizzes.questions.index', $quiz) }}" class="btn btn-info btn-sm">Questions</a>
                                                    <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-primary btn-sm">Edit</a>
                                                    @if($quiz->is_published)
                                                        <form action="{{ route('quizzes.unpublish', $quiz) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm">Unpublish</button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('quizzes.publish', $quiz) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm">Publish</button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this quiz?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No quizzes created yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- Student View -->
                        <div class="row">
                            @forelse($quizzes as $quiz)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $quiz->title }}</h5>
                                            <p class="card-text">{{ Str::limit($quiz->description, 100) }}</p>
                                            <ul class="list-unstyled">
                                                <li><strong>Duration:</strong> {{ $quiz->duration }} minutes</li>
                                                <li><strong>Questions:</strong> {{ $quiz->questions->count() }}</li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            @php
                                                $attempt = $quiz->attempts()->where('user_id', Auth::id())->latest()->first();
                                            @endphp
                                            
                                            @if(!$attempt)
                                                <form action="{{ route('quiz-attempts.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                                                    <button type="submit" class="btn btn-primary">Start Quiz</button>
                                                </form>
                                            @elseif($attempt->completed_at)
                                                <a href="{{ route('results.show', $attempt) }}" class="btn btn-info">View Result</a>
                                            @else
                                                <a href="{{ route('quiz-attempts.show', $attempt) }}" class="btn btn-warning">Continue Quiz</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        No quizzes available at the moment.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    @endif

                    @if($quizzes->hasPages())
                        <div class="mt-4">
                            {{ $quizzes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 