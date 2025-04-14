<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isLecturer()) {
            // For lecturers, show all quiz results
            $quizzes = Quiz::where('user_id', $user->id)
                ->with(['attempts' => function ($query) {
                    $query->with('user');
                }])
                ->get();

            return view('results.lecturer-index', compact('quizzes'));
        } else {
            // For students, show their quiz attempts
            $attempts = QuizAttempt::where('user_id', $user->id)
                ->with('quiz')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('results.student-index', compact('attempts'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $attempt = QuizAttempt::with(['quiz', 'answers.question'])->findOrFail($id);

        if ($user->isLecturer()) {
            // Verify that the lecturer owns the quiz
            if ($attempt->quiz->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            // Verify that the student owns the attempt
            if ($attempt->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('results.show', compact('attempt'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
