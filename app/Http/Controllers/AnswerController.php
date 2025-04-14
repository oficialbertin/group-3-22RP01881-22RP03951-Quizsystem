<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Option;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:options,id',
            'answer_text' => 'nullable|string|max:1000',
        ]);

        $quizAttempt = QuizAttempt::findOrFail($request->quiz_attempt_id);
        $question = Question::findOrFail($request->question_id);

        // Check if the quiz attempt belongs to the authenticated user
        if ($quizAttempt->user_id !== Auth::id()) {
            return redirect()->route('quizzes.index')
                ->with('error', 'Unauthorized access');
        }

        // Check if the quiz attempt is still in progress
        if ($quizAttempt->completed_at) {
            return redirect()->route('quiz-attempts.show', $quizAttempt->id)
                ->with('error', 'This quiz attempt has already been completed');
        }

        // Check if the question belongs to the quiz
        if ($question->quiz_id !== $quizAttempt->quiz_id) {
            return redirect()->route('quiz-attempts.show', $quizAttempt->id)
                ->with('error', 'Invalid question for this quiz');
        }

        // Check if an answer already exists for this question in this attempt
        $existingAnswer = Answer::where('quiz_attempt_id', $quizAttempt->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            return redirect()->route('quiz-attempts.show', $quizAttempt->id)
                ->with('error', 'You have already answered this question');
        }

        // Validate answer based on question type
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
            if (!$request->option_id) {
                return redirect()->route('quiz-attempts.show', $quizAttempt->id)
                    ->with('error', 'Please select an option');
            }

            $option = Option::findOrFail($request->option_id);
            if ($option->question_id !== $question->id) {
                return redirect()->route('quiz-attempts.show', $quizAttempt->id)
                    ->with('error', 'Invalid option for this question');
            }

            $isCorrect = $option->is_correct;
            $marksObtained = $isCorrect ? $question->marks : 0;
        } else {
            if (!$request->answer_text) {
                return redirect()->route('quiz-attempts.show', $quizAttempt->id)
                    ->with('error', 'Please provide an answer');
            }

            // For short answer questions, we'll mark them as correct by default
            // The lecturer can review and adjust the marks later
            $isCorrect = true;
            $marksObtained = $question->marks;
        }

        $answer = Answer::create([
            'quiz_attempt_id' => $quizAttempt->id,
            'question_id' => $question->id,
            'option_id' => $request->option_id,
            'answer_text' => $request->answer_text,
            'is_correct' => $isCorrect,
            'marks_obtained' => $marksObtained,
        ]);

        return redirect()->route('quiz-attempts.show', $quizAttempt->id)
            ->with('success', 'Answer submitted successfully');
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
        $request->validate([
            'marks_obtained' => 'required|numeric|min:0|max:' . $request->max_marks,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $answer = Answer::findOrFail($id);
        
        // Check if the authenticated user is the lecturer who owns the quiz
        if ($answer->question->quiz->user_id !== Auth::id()) {
            return redirect()->route('quizzes.index')
                ->with('error', 'Unauthorized access');
        }

        $answer->update([
            'marks_obtained' => $request->marks_obtained,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('quiz-attempts.show', $answer->quiz_attempt_id)
            ->with('success', 'Answer updated successfully');
    }
} 