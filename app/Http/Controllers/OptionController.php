<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('lecturer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $question_id = request('question_id');
        if (!$question_id) {
            return redirect()->route('questions.index')
                ->with('error', 'Question ID is required');
        }

        $question = Question::findOrFail($question_id);
        
        // Check if the authenticated user owns the quiz
        if ($question->quiz->user_id !== Auth::id()) {
            return redirect()->route('questions.index')
                ->with('error', 'Unauthorized access');
        }

        $options = $question->options()->orderBy('created_at', 'asc')->get();
        return view('options.index', compact('options', 'question'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $question_id = request('question_id');
        if (!$question_id) {
            return redirect()->route('questions.index')
                ->with('error', 'Question ID is required');
        }

        $question = Question::findOrFail($question_id);
        
        // Check if the authenticated user owns the quiz
        if ($question->quiz->user_id !== Auth::id()) {
            return redirect()->route('questions.index')
                ->with('error', 'Unauthorized access');
        }

        // Check if the question type supports options
        if (!in_array($question->question_type, ['multiple_choice', 'true_false'])) {
            return redirect()->route('questions.index', ['quiz_id' => $question->quiz_id])
                ->with('error', 'This question type does not support options');
        }

        return view('options.create', compact('question'));
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
            'question_id' => 'required|exists:questions,id',
            'option_text' => 'required|string|max:500',
            'is_correct' => 'required|boolean',
        ]);

        $question = Question::findOrFail($request->question_id);
        
        // Check if the authenticated user owns the quiz
        if ($question->quiz->user_id !== Auth::id()) {
            return redirect()->route('questions.index')
                ->with('error', 'Unauthorized access');
        }

        // Check if the question type supports options
        if (!in_array($question->question_type, ['multiple_choice', 'true_false'])) {
            return redirect()->route('questions.index', ['quiz_id' => $question->quiz_id])
                ->with('error', 'This question type does not support options');
        }

        // For true/false questions, ensure only one correct option
        if ($question->question_type === 'true_false') {
            $existingCorrectOption = $question->options()->where('is_correct', true)->first();
            if ($existingCorrectOption && $request->is_correct) {
                return redirect()->route('options.index', ['question_id' => $question->id])
                    ->with('error', 'True/False questions can only have one correct option');
            }
        }

        $option = Option::create([
            'question_id' => $request->question_id,
            'option_text' => $request->option_text,
            'is_correct' => $request->is_correct,
        ]);

        return redirect()->route('options.index', ['question_id' => $question->id])
            ->with('success', 'Option created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $option = Option::findOrFail($id);
        
        // Check if the authenticated user owns the quiz
        if ($option->question->quiz->user_id !== Auth::id()) {
            return redirect()->route('questions.index')
                ->with('error', 'Unauthorized access');
        }

        return view('options.edit', compact('option'));
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
            'option_text' => 'required|string|max:500',
            'is_correct' => 'required|boolean',
        ]);

        $option = Option::findOrFail($id);
        
        // Check if the authenticated user owns the quiz
        if ($option->question->quiz->user_id !== Auth::id()) {
            return redirect()->route('questions.index')
                ->with('error', 'Unauthorized access');
        }

        // For true/false questions, ensure only one correct option
        if ($option->question->question_type === 'true_false' && $request->is_correct) {
            $existingCorrectOption = $option->question->options()
                ->where('is_correct', true)
                ->where('id', '!=', $id)
                ->first();
            if ($existingCorrectOption) {
                return redirect()->route('options.index', ['question_id' => $option->question_id])
                    ->with('error', 'True/False questions can only have one correct option');
            }
        }

        $option->update([
            'option_text' => $request->option_text,
            'is_correct' => $request->is_correct,
        ]);

        return redirect()->route('options.index', ['question_id' => $option->question_id])
            ->with('success', 'Option updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $option = Option::findOrFail($id);
        
        // Check if the authenticated user owns the quiz
        if ($option->question->quiz->user_id !== Auth::id()) {
            return redirect()->route('questions.index')
                ->with('error', 'Unauthorized access');
        }

        $question_id = $option->question_id;
        $option->delete();

        return redirect()->route('options.index', ['question_id' => $question_id])
            ->with('success', 'Option deleted successfully');
    }
} 