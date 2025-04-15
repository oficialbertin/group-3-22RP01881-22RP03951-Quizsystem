<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->isLecturer()) {
            // For lecturers, show all attempts for their quizzes
            $attempts = QuizAttempt::whereHas('quiz', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['user', 'quiz'])
            ->latest()
            ->paginate(10);
        } else {
            // For students, show only their attempts
            $attempts = QuizAttempt::where('user_id', Auth::id())
                ->with('quiz')
                ->latest()
                ->paginate(10);
        }

        return view('results.index', compact('attempts'));
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
        $quiz = Quiz::findOrFail($request->quiz_id);

        // Check if quiz is published and available
        if (!$quiz->is_published) {
            return back()->with('error', 'This quiz is not available.');
        }

        // Check if quiz is within time constraints
        if ($quiz->start_time && $quiz->start_time->isFuture()) {
            return back()->with('error', 'This quiz has not started yet.');
        }

        if ($quiz->end_time && $quiz->end_time->isPast()) {
            return back()->with('error', 'This quiz has ended.');
        }

        // Check if user already has an incomplete attempt
        $existingAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('end_time')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('quiz-attempts.show', $existingAttempt);
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'score' => 0
        ]);

        return redirect()->route('quiz-attempts.show', $attempt)
            ->with('success', 'Quiz attempt started. Good luck!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(QuizAttempt $quizAttempt)
    {
        // Ensure the user has permission to view this attempt
        if (Auth::user()->role === 'student' && $quizAttempt->user_id !== Auth::id()) {
            abort(403);
        }

        if (Auth::user()->role === 'lecturer' && $quizAttempt->quiz->user_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships
        $quizAttempt->load(['quiz', 'user', 'answers.question', 'answers.option']);

        // If the quiz is completed, show results
        if ($quizAttempt->end_time) {
            return view('results.show', compact('quizAttempt'));
        }

        // Otherwise show the quiz questions
        return view('quiz-attempts.show', compact('quizAttempt'));
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

    public function submit(Request $request, QuizAttempt $quizAttempt)
    {
        // Check if this attempt belongs to the authenticated user
        if ($quizAttempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the attempt is already completed
        if ($quizAttempt->end_time) {
            return redirect()->route('results.show', $quizAttempt)
                ->with('error', 'This quiz attempt has already been submitted.');
        }

        // Process each answer
        $totalScore = 0;
        foreach ($request->answers ?? [] as $questionId => $answer) {
            $question = $quizAttempt->quiz->questions()->find($questionId);
            
            if (!$question) {
                continue;
            }

            $isCorrect = false;
            $marksObtained = 0;

            if ($question->type === 'multiple_choice' && isset($answer['option_id'])) {
                $option = $question->options()->find($answer['option_id']);
                if ($option && $option->is_correct) {
                    $isCorrect = true;
                    $marksObtained = $question->marks;
                }
            }

            // Create the answer record
            $quizAttempt->answers()->create([
                'question_id' => $questionId,
                'option_id' => $answer['option_id'] ?? null,
                'answer_text' => $answer['answer_text'] ?? null,
                'is_correct' => $isCorrect,
                'marks_obtained' => $marksObtained
            ]);

            $totalScore += $marksObtained;
        }

        // Update the quiz attempt
        $quizAttempt->update([
            'end_time' => now(),
            'score' => $totalScore
        ]);

        return redirect()->route('results.show', $quizAttempt)
            ->with('success', 'Quiz submitted successfully!');
    }
}
