<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->isLecturer()) {
            // Get all attempts for quizzes created by the lecturer
            $attempts = QuizAttempt::whereHas('quiz', function($query) {
                $query->where('user_id', Auth::id());
            })->with(['quiz', 'user'])
              ->latest('completed_at')
              ->paginate(10);

            return view('results.lecturer-index', compact('attempts'));
        } else {
            // Get student's own attempts
            $attempts = Auth::user()->attempts()
                ->with('quiz')
                ->latest('completed_at')
                ->paginate(10);

            return view('results.student-index', compact('attempts'));
        }
    }

    public function show(QuizAttempt $attempt)
    {
        // Check if user has permission to view this result
        if (!Auth::user()->isLecturer() && Auth::id() !== $attempt->user_id) {
            abort(403);
        }

        // For lecturers, check if the quiz belongs to them
        if (Auth::user()->isLecturer() && $attempt->quiz->user_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships
        $attempt->load(['quiz', 'user', 'answers.question.options']);

        return view('results.show', compact('attempt'));
    }
} 