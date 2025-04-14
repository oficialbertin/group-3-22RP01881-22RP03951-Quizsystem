@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Quiz Results</h5>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Time Taken</th>
                                    <th>Completed At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->quiz->title }}</td>
                                        <td>{{ $attempt->score }}/{{ $attempt->total_questions }}</td>
                                        <td>{{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%</td>
                                        <td>{{ $attempt->completed_at ? $attempt->completed_at->diffForHumans($attempt->started_at, true) : 'In Progress' }}</td>
                                        <td>{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : '-' }}</td>
                                        <td>
                                            @if($attempt->completed_at)
                                                <a href="{{ route('results.show', $attempt->id) }}" class="btn btn-primary btn-sm">View Details</a>
                                            @else
                                                <a href="{{ route('quiz-attempts.show', $attempt->id) }}" class="btn btn-warning btn-sm">Continue Quiz</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">You haven't attempted any quizzes yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($attempts->hasPages())
                        <div class="mt-4">
                            {{ $attempts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 