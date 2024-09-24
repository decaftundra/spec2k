<?php

namespace App\Http\Controllers;

use App\User;
use App\Alert;
use App\Issue;
use App\Comment;
use App\Mail\IssueCreated;
use Illuminate\Http\Request;
use App\Policies\IssuePolicy;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class IssueTrackerController extends Controller
{
    /**
     * Show a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Issue::class);
        
        $status = $request->status == 'all' ? NULL : ['new', 'open'];
        $search = $request->search ?? NULL;
        
        $issues = Issue::status($status)
            ->search($search)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        $request->flash();
        
        return view('issues.index')
            ->with('issues', $issues)
            ->with('kindIcons', Issue::$kindIcons)
            ->with('priorityIcons', Issue::$priorityIcons)
            ->with('statuses', Issue::$statuses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Issue::class);
        
        return view('issues.create')
            ->with('kinds', Issue::$kinds)
            ->with('priorities', Issue::$priorities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Issue::class);
        
        $request->validate(Issue::getRules());
        
        $issue = new Issue;
        $issue->title = $request->title;
        $issue->content = $request->content;
        $issue->kind = $request->kind;
        $issue->priority = $request->priority;
        $issue->status = 'new';
        $issue->posted_by = auth()->user()->fullname;
        $issue->save();
            
        $supportEmail = config('support.email');
        
        if (!$supportEmail) {
            throw new \Exception('Could not find support email address.');
        }
        
        // Try to get support user if they exist.
        $supportUser = User::where('email', $supportEmail)->first();
        
        if ($supportUser) {
            Mail::to($supportUser)->send(new IssueCreated($issue)); // Notify support.
        } else {
            Mail::to($supportEmail)->send(new IssueCreated($issue)); // Notify support.
        }
        
        return redirect(route('issue-tracker.index'))
            ->with(Alert::success('Issue raised successfully! Technical support have been notified.'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $issue = Issue::with('comments')->findOrFail($id);
        
        $this->authorize('edit', $issue);
        
        return view('issues.edit')
            ->with('issue', $issue)
            ->with('kinds', Issue::$kinds)
            ->with('statuses', Issue::$statuses)
            ->with('priorities', Issue::$priorities);
    }
    
    /**
     * Update a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(Issue::getUpdateRules());
        
        $issue = Issue::findOrFail($id);
        
        $this->authorize('edit', $issue);
        
        $issue->kind = $request->kind;
        $issue->priority = $request->priority;
        $issue->status = $request->status;
        $issue->save();
        
        if ($request->filled('comment')) {
            $comment = new Comment;
            $comment->content = $request->comment;
            $comment->posted_by = auth()->user()->fullname;
            $issue->comments()->save($comment);
        }
        
        return redirect(route('issue-tracker.index'))
            ->with(Alert::success('Issue updated successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $issue = Issue::with('comments')->findOrFail($id);
        
        $this->authorize('show', $issue);
        
        return view('issues.show')
            ->with('issue', $issue);
    }
}
