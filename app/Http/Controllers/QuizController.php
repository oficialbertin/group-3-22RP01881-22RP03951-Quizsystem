<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
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
    public function index()
    {
        if (Auth::user()->isLecturer()) {
            $quizzes = Quiz::where('user_id', Auth::id())->latest()->paginate(10);
        } else {
            $quizzes = Quiz::where('is_published', true)
                ->where(function($query) {
                    $query->whereNull('start_time')
                        ->orWhere('start_time', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('end_time')
                        ->orWhere('end_time', '>=', now());
                })
                ->whereHas('questions')
                ->latest()
                ->paginate(10);
        }
        
        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('quizzes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $quiz = Auth::user()->quizzes()->create($validated);

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        if (Auth::user()->isStudent() && !$quiz->is_published) {
            abort(403);
        }

        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        return view('quizzes.edit', compact('quiz'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        try {
            DB::beginTransaction();
            
            $quiz->update($validated);
            
            DB::commit();
            return redirect()->route('quizzes.show', $quiz)
                ->with('success', 'Quiz updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update quiz: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update quiz: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->delete();

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function publish(Quiz $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->update(['is_published' => true]);

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', 'Quiz published successfully.');
    }

    public function unpublish(Quiz $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->update(['is_published' => false]);

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', 'Quiz unpublished successfully.');
    }
}
