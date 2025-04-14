@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Options for Question</h5>
                    <a href="{{ route('options.create', ['question_id' => $question->id]) }}" class="btn btn-primary">
                        Add Option
                    </a>
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

                    @if($options->isEmpty())
                        <div class="alert alert-info">
                            No options found for this question. Add your first option!
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($options as $option)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $option->option_text }}
                                            @if($option->is_correct)
                                                <span class="badge bg-success ms-2">Correct</span>
                                            @endif
                                        </div>
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
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('questions.show', $question->id) }}" class="btn btn-secondary">
                            Back to Question
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 