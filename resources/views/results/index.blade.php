@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ Auth::user()->isLecturer() ? 'Quiz Results' : 'My Quiz Results' }}</h3>
                </div>

                <div class="card-body">
                    @if($attempts->isEmpty())
                        <div class="alert alert-info">
                            No quiz results found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        @if(Auth::user()->isLecturer())
                                            <th>Student</th>
                                        @endif
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Completed At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $attempt)
                                        <tr>
                                            @if(Auth::user()->isLecturer())
                                                <td>{{ $attempt->user->name }}</td>
                                            @endif
                                            <td>{{ $attempt->quiz->title }}</td>
                                            <td>{{ $attempt->score }} / {{ $attempt->quiz->total_marks }}</td>
                                            <td>{{ $attempt->end_time->format('M d, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('results.show', $attempt) }}" class="btn btn-sm btn-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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