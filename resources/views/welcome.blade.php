@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @guest
                <div class="card">
                    <div class="card-header">Welcome to Online Quiz System</div>
                    <div class="card-body text-center">
                        <h4>Please login or register to continue</h4>
                        <div class="mt-4">
                            <a href="{{ route('login') }}" class="btn btn-primary me-2">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                        </div>
                    </div>
                </div>
            @else
                @if(Auth::user()->isLecturer())
                    <!-- Lecturer Dashboard -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Lecturer Dashboard</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h3>{{ $user->quizzes->count() }}</h3>
                                            <p class="mb-0">Total Quizzes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h3>{{ $user->quizzes->where('is_published', true)->count() }}</h3>
                                            <p class="mb-0">Published Quizzes</p>
                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                            </div>

                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('quizzes.create') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Create New Quiz
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('quizzes.index') }}" class="btn btn-info w-100">
                                        <i class="fas fa-list"></i> Manage Quizzes
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('results.index') }}" class="btn btn-success w-100">
                                        <i class="fas fa-chart-bar"></i> View Results
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="#" class="btn btn-secondary w-100">
                                        <i class="fas fa-cog"></i> Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Student Dashboard -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Student Dashboard</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h3>{{ $user->attempts->count() }}</h3>
                                            <p class="mb-0">Quizzes Attempted</p>
                                        </div>
                            </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h3>{{ $user->attempts->where('completed_at', '!=', null)->count() }}</h3>
                                            <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h3>{{ $user->attempts->avg('score') ? number_format($user->attempts->avg('score'), 1) : '0.0' }}%</h3>
                                            <p class="mb-0">Average Score</p>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('quizzes.index') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-book"></i> Available Quizzes
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('results.index') }}" class="btn btn-info w-100">
                                        <i class="fas fa-chart-line"></i> My Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endguest
                    </div>
                </div>
            </div>
@endsection
