<?php

namespace Tests\Feature;

use App\Issue;
use App\Comment;
use Tests\TestCase;
use App\Mail\IssueCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssuesTest extends TestCase
{
    use WithFaker;
    
    /**
     * Test the issues index.
     *
     * @return void
     */
    public function testIssuesIndex()
    {
        $issues = factory(Issue::class, 5)->create();
        $openIssues = factory(Issue::class, 5)->states('open_issue')->create();
        
        $openIssuesCount = Issue::whereIn('status', ['new', 'open'])->get()->count();
        
        $this->actingAs($this->user)
            ->call('GET', route('issue-tracker.index'))
            ->assertSee("Displaying 1 to $openIssuesCount of $openIssuesCount issues.")
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('issue-tracker.index') . '?status=all')
            ->assertSee("Displaying 1 to 10 of 10 issues.")
            ->assertStatus(200);
    }
    
    /**
     * Test creating an issue.
     *
     * @return void
     */
    public function testCreateIssue()
    {
        Mail::fake();
        
        $this->actingAs($this->user)
            ->call('GET', route('issue-tracker.create'))
            ->assertSee('Create New Issue')
            ->assertStatus(200);
        
        // All issues created automatically have a status of new.
        $issue = factory(Issue::class)->raw(['status' => 'new']);
        unset($issue['posted_by']);
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('issue-tracker.store'), $issue)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Issue raised successfully! Technical support have been notified.');
            
        Mail::assertSent(IssueCreated::class);
        
        $this->assertDatabaseHas('issues', $issue);
    }
    
    /**
     * Test showing an issue.
     *
     * @return void
     */
    public function testShowIssue()
    {
        // Create some new issues.
        $issues = factory(Issue::class, 10)->create(['status' => 'new']);
        
        // Pick a random issue.
        $issue = Issue::inRandomOrder()->first();
        
        $this->actingAs($this->user)
            ->call('GET', route('issue-tracker.show', $issue->id))
            ->assertSee($issue->content)
            ->assertStatus(200);
    }
    
    /**
     * Make sure a normal user can't edit an issue.
     *
     * @return void
     */
    public function testUserCantEditIssue()
    {
        // Create some new issues.
        $issues = factory(Issue::class, 10)->create(['status' => 'new']);
        
        // Pick a random issue.
        $issue = Issue::inRandomOrder()->first();
        
        $this->actingAs($this->user)
            ->call('GET', route('issue-tracker.edit', $issue->id))
            ->assertStatus(403);
    }
    
    /**
     * Test editing an issue and adding a comment.
     *
     * @return void
     */
    public function testEditIssue()
    {
        // Create some new issues.
        $issues = factory(Issue::class, 10)->create(['status' => 'new']);
        
        // Pick a random issue.
        $issue = Issue::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('issue-tracker.edit', $issue->id))
            ->assertSee('Edit Issue')
            ->assertStatus(200);
            
        $comment = $this->faker->sentence;
        
        $attributes = [
            'kind' => array_rand(Issue::$kinds),
            'priority' => array_rand(Issue::$priorities),
            'status' => array_rand(Issue::$statuses),
            'comment' => $comment
        ];
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('issue-tracker.update', $issue->id), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Issue updated successfully!');
            
        unset($attributes['comment']);
            
        $this->assertDatabaseHas('issues', $attributes);
        $this->assertDatabaseHas('comments', ['content' => $comment, 'posted_by' => $this->dataAdminUser->fullname]);
        
        // Make sure comment is visible on show issue page.
        $this->actingAs($this->user)
            ->call('GET', route('issue-tracker.show', $issue->id))
            ->assertSee($comment)
            ->assertSee($issue->content)
            ->assertStatus(200);
    }
}
