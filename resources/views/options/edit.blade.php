@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Option</h5>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h6>Question Text</h6>
                        <p>{{ $option->question->question_text }}</p>
                    </div>

                    <form action="{{ route('options.update', $option->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="option_text" class="form-label">Option Text</label>
                            <input type="text" 
                                   class="form-control @error('option_text') is-invalid @enderror" 
                                   id="option_text" 
                                   name="option_text" 
                                   value="{{ old('option_text', $option->option_text) }}" 
                                   required>
                            @error('option_text')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_correct') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="is_correct" 
                                       name="is_correct" 
                                       value="1" 
                                       {{ old('is_correct', $option->is_correct) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_correct">
                                    This is the correct answer
                                </label>
                                @error('is_correct')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('options.index', ['question_id' => $option->question_id]) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update Option
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 