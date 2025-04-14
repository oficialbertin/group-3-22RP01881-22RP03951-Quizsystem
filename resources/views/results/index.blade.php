@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Quiz Results</h2>
        </div>
        <div class="card-body">
            @if(auth()->user()->isLecturer())
                <table class="table">
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Student</th>
                            <th>Score</th>
                            <th>Completed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $attempt)
                            <tr>
                                <td>{{ $attempt->quiz->title }}</td>
                                <td>{{ $attempt->user->name }}</td>
                                <td>{{ $attempt->score }}/{{ $attempt->total_questions }}</td>
                                <td>{{ $attempt->completed_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('results.show', $attempt->id) }}" class="btn btn-primary btn-sm">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="row">
                    @foreach($results as $attempt)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $attempt->quiz->title }}</h5>
                                    <p class="card-text">
                                        Score: {{ $attempt->score }}/{{ $attempt->total_questions }}<br>
                                        Percentage: {{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%<br>
                                        Completed: {{ $attempt->completed_at->format('Y-m-d H:i:s') }}
                                    </p>
                                    <a href="{{ route('results.show', $attempt->id) }}" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 