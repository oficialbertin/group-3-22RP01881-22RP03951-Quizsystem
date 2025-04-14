@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Question Details</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h6>Question Text</h6>
                        <p>{{ $question->question_text }}</p>
                    </div>

                    <div class="mb-4">
                        <h6>Question Type</h6>
                        <p>{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</p>
                    </div>

                    <div class="mb-4">
                        <h6>Marks</h6>
                        <p>{{ $question->marks }}</p>
                    </div>

                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                        <div class="mb-4">
                            <h6>Options</h6>
                            @if($question->options->isEmpty())
                                <p class="text-muted">No options available.</p>
                            @else
                                <div class="list-group">
                                    @foreach($question->options as $option)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    {{ $option->option_text }}
                                                    @if($option->is_correct)
                                                        <span class="badge bg-success ms-2">Correct</span>
                                                    @endif
                                                </div>
                                                @if(Auth::user()->isLecturer() && $question->quiz->user_id === Auth::id())
                                                    <div class="btn-group">
                                                        <a href="{{ route('options.edit', $option->id) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            Edit
                                                        </a>
                                                        <form action="{{ route('options.destroy', $option->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this option?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        @if(Auth::user()->isLecturer() && $question->quiz->user_id === Auth::id())
                            <div>
                                <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-primary">
                                    Edit Question
                                </a>
                                @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                                    <a href="{{ route('options.create', ['question_id' => $question->id]) }}" 
                                       class="btn btn-secondary">
                                        Add Option
                                    </a>
                                @endif
                            </div>
                        @endif
                        <a href="{{ route('questions.index', ['quiz_id' => $question->quiz_id]) }}" class="btn btn-secondary">
                            Back to Questions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 