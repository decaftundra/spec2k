<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    /**
     * Get the comments for the issue.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
    
    /**
     * Search issues by title and content.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search = NULL)
    {
        if (!$search) return $query;
        
        return $query->where('content', 'LIKE', "%$search%")
            ->OrWhere('title', 'LIKE', "%$search%");
    }
    
    /**
     * Search issues by title and content.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status = NULL)
    {
        if (!$status) return $query;
        
        return $query->whereIn('status', $status);
    }
    
    /**
     * Issue kinds.
     *
     * @var array
     */
    public static $kinds = [
        'proposal' => 'Proposal',
        'bug' => 'Bug',
        'enhancement' => 'Enhancement',
        'task' => 'Task'
    ];
    
    /**
     * Issue priorities with icons.
     *
     * @var array
     */
    public static $priorityIcons = [
        'trivial' => '<i class="fas fa-exclamation-circle text-info"></i> Trivial',
        'minor' => '<i class="fas fa-exclamation-circle text-warning"></i> Minor',
        'major' => '<i class="fas fa-exclamation-triangle text-danger"></i> Major',
        'critical' => '<i class="fas fa-file-medical-alt text-danger"></i> Critical',
        'blocker' => '<i class="fas fa-ban text-danger"></i> Blocker'
    ];
    
    /**
     * Priorities array.
     *
     * @var array
     */
    public static $priorities = [
        'trivial' => 'Trivial',
        'minor' => 'Minor',
        'major' => 'Major',
        'critical' => 'Critical',
        'blocker' => 'Blocker'
    ];
    
    /**
     * Issue kinds with display icons.
     *
     * @var array
     */
    public static $kindIcons = [
        'proposal' => '<i class="far fa-lightbulb text-warning"></i> Proposal',
        'bug' => '<i class="fas fa-bug text-danger"></i> Bug',
        'enhancement' => '<i class="fas fa-wrench text-success"></i> Enhancement',
        'task' => '<i class="fas fa-thumbtack text-primary"></i> Task'
    ];
    
    /**
     * Issue statuses with display icons.
     *
     * @var array
     */
    public static $statuses = [
        'new' => '<span class="label label-primary">New</span>',
        'open' => '<span class="label label-primary">Open</span>',
        'on hold' => '<span class="label label-warning">On Hold</span>',
        'resolved' => '<span class="label label-success">Resolved</span>',
        'duplicate' => '<span class="label label-default">Duplicate</span>',
        'invalid' => '<span class="label label-danger">Invalid</span>',
        'wontfix' => '<span class="label label-danger">Won\'t Fix</span>',
        'closed' => '<span class="label label-success">Closed</span>'
    ];
    
    /**
     * Get the issue validation rules array.
     *
     * @return array
     */
    public static function getRules()
    {
        return [
            'title' => 'required',
            'content' => 'required',
            'kind' => 'required|in:'.implode(',', array_keys(self::$kinds)),
            'priority' => 'required|in:'.implode(',', array_keys(self::$priorities))
        ];
    }
    
    /**
     * Get the issue validation rules array.
     *
     * @return array
     */
    public static function getUpdateRules()
    {
        return [
            'kind' => 'required|in:'.implode(',', array_keys(self::$kinds)),
            'priority' => 'required|in:'.implode(',', array_keys(self::$priorities)),
            'status' => 'required|in:'.implode(',', array_keys(self::$statuses))
        ];
    }
    
    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return ucfirst($this->title);
    }
    
    /**
     * Get the content.
     *
     * @return string
     */
    public function getContent()
    {
        return nl2br(ucfirst($this->content));
    }
    
    /**
     * Get the kind icon.
     *
     * @return string
     */
    public function getKind()
    {
        return self::$kindIcons[$this->kind];
    }
    
    /**
     * Get the status.
     *
     * @return string
     */
    public function getStatus()
    {
        return self::$statuses[$this->status];
    }
    
    /**
     * Get the prority icon.
     *
     * @return string
     */
    public function getPriority()
    {
        return self::$priorityIcons[$this->priority];
    }
    
    /**
     * Retrieve the name of the user that raised the issue.
     *
     * @return string
     */
    public function getPostedBy()
    {
        return $this->posted_by;
    }
}
