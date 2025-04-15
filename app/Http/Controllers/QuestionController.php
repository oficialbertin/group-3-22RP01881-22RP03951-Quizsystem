<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('lecturer')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Quiz $quiz)
    {
        $this->authorize('view', $quiz);
        $questions = $quiz->questions()->with('options')->get();
        return view('questions.index', compact('quiz', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        return view('questions.create', compact('quiz'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        try {
            // Log the incoming request data
            Log::info('Question creation request data:', $request->all());

            $validated = $request->validate([
                'text' => 'required|string',
                'type' => 'required|in:multiple_choice,true_false,short_answer',
                'marks' => 'required|integer|min:1',
                'options' => 'required_if:type,multiple_choice|array|min:2',
                'options.*' => 'required_if:type,multiple_choice|string',
                'correct_option' => 'required_if:type,multiple_choice|integer|min:0',
                'true_false_answer' => 'required_if:type,true_false|boolean',
            ]);

            Log::info('Validated data:', $validated);

            DB::beginTransaction();

            $question = $quiz->questions()->create([
                'question_text' => $validated['text'],
                'question_type' => $validated['type'],
                'marks' => $validated['marks'],
            ]);

            Log::info('Created question:', $question->toArray());

            if ($validated['type'] === 'multiple_choice') {
                foreach ($validated['options'] as $index => $optionText) {
                    $option = $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => $index == $validated['correct_option']
                    ]);
                    Log::info('Created multiple choice option:', $option->toArray());
                }
            } elseif ($validated['type'] === 'true_false') {
                // Create True option
                $trueOption = $question->options()->create([
                    'option_text' => 'True',
                    'is_correct' => $validated['true_false_answer'] == 1
                ]);
                Log::info('Created true option:', $trueOption->toArray());
                
                // Create False option
                $falseOption = $question->options()->create([
                    'option_text' => 'False',
                    'is_correct' => $validated['true_false_answer'] == 0
                ]);
                Log::info('Created false option:', $falseOption->toArray());
            }

            DB::commit();
            return redirect()->route('quizzes.questions.index', $quiz)
                ->with('success', 'Question added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the detailed error
            Log::error('Failed to create question. Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()
                ->with('error', 'Failed to create question. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::with(['options', 'quiz'])->findOrFail($id);
        
        // If user is not a lecturer, check if the quiz is published and available
        if (!Auth::user()->isLecturer()) {
            if (!$question->quiz->is_published || 
                ($question->quiz->start_time && $question->quiz->start_time > now()) ||
                ($question->quiz->end_time && $question->quiz->end_time < now())) {
                return redirect()->route('quizzes.index')
                    ->with('error', 'This quiz is not available');
            }
        }
        // If user is a lecturer, check if they own the quiz
        else if ($question->quiz->user_id !== Auth::id()) {
            return redirect()->route('quizzes.index')
                ->with('error', 'Unauthorized access');
        }

        return view('questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz, Question $question)
    {
        $this->authorize('update', $quiz);
        $question->load('options');
        return view('questions.edit', compact('quiz', 'question'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $this->authorize('update', $quiz);

        try {
            // Log the incoming request data
            Log::info('Question update request data:', $request->all());

            $validated = $request->validate([
                'text' => 'required|string',
                'type' => 'required|in:multiple_choice,true_false,short_answer',
                'marks' => 'required|integer|min:1',
                'options' => 'required_if:type,multiple_choice|array|min:2',
                'options.*' => 'required_if:type,multiple_choice|string',
                'correct_option' => 'required_if:type,multiple_choice|integer|min:0',
                'true_false_answer' => 'required_if:type,true_false|boolean',
            ]);

            Log::info('Validated data:', $validated);

            DB::beginTransaction();

            // Update question
            $updateData = [
                'question_text' => $validated['text'],
                'question_type' => $validated['type'],
                'marks' => $validated['marks'],
            ];
            Log::info('Updating question with data:', $updateData);
            $question->update($updateData);

            Log::info('Updated question:', $question->fresh()->toArray());

            // Delete existing options
            Log::info('Deleting existing options for question ID: ' . $question->id);
            $question->options()->delete();

            if ($validated['type'] === 'multiple_choice') {
                Log::info('Processing multiple choice options');
                foreach ($validated['options'] as $index => $optionText) {
                    $isCorrect = $index == $validated['correct_option'];
                    Log::info("Creating option {$index}: {$optionText} (correct: " . ($isCorrect ? 'yes' : 'no') . ")");
                    
                    $option = $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => $isCorrect
                    ]);
                    Log::info('Created option:', $option->toArray());
                }
            } elseif ($validated['type'] === 'true_false') {
                Log::info('Processing true/false options');
                // Create True option
                $trueOption = $question->options()->create([
                    'option_text' => 'True',
                    'is_correct' => $validated['true_false_answer'] == 1
                ]);
                Log::info('Created true option:', $trueOption->toArray());
                
                // Create False option
                $falseOption = $question->options()->create([
                    'option_text' => 'False',
                    'is_correct' => $validated['true_false_answer'] == 0
                ]);
                Log::info('Created false option:', $falseOption->toArray());
            }

            DB::commit();
            Log::info('Question update completed successfully');

            session()->flash('success', 'Question updated successfully.');
            return redirect()->route('quizzes.questions.index', $quiz);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update question. Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            session()->flash('error', 'Failed to update question: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz, Question $question)
    {
        $this->authorize('update', $quiz);
        
        try {
            $question->delete();
            return redirect()->route('quizzes.questions.index', $quiz)
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question. Please try again.');
        }
    }
}
