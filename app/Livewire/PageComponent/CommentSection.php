<?php

namespace App\Livewire\PageComponent;

use App\Models\Replies;
use Livewire\Component;
use App\Models\Comments;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class CommentSection extends Component
{
    use WithPagination;

    public $productId;
    public $commentText = '';
    public $replyText = '';
    public $replyingTo = null;
    public $perPage = 5;
    public $sortDirection = 'desc';

    // Updated to use dispatchBrowserEvent instead of emit
    protected $listeners = ['commentAdded' => '$refresh', 'replyAdded' => '$refresh'];

    protected $rules = [
        'commentText' => 'required|min:3',
        'replyText' => 'required|min:3',
    ];

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function render()
    {
        $comments = Comments::where('product_id', $this->productId)
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.page-component.comment-section', [
            'comments' => $comments,
        ]);
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'commentText' => 'required|min:3',
        ]);

        Comments::create([
            'user_id' => Auth::id(),
            'product_id' => $this->productId,
            'body' => $this->commentText,
        ]);

        $this->commentText = '';
        // Changed from emit to dispatch with message
        $this->dispatch('commentAdded')->self();
        $this->dispatch('notify', ['message' => 'Comment added successfully!', 'type' => 'success']);
    }

    public function startReply($commentId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->replyingTo = $commentId;
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyText = '';
    }

    public function addReply($commentId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'replyText' => 'required|min:3',
        ]);

        Replies::create([
            'comment_id' => $commentId,
            'user_id' => Auth::id(),
            'body' => $this->replyText,
        ]);

        $this->replyText = '';
        $this->replyingTo = null;
        // Changed from emit to dispatch with message
        $this->dispatch('replyAdded')->self();
        $this->dispatch('notify', ['message' => 'Reply added successfully!', 'type' => 'success']);
    }

    public function deleteComment($commentId)
    {
        $comment = Comments::find($commentId);

        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->can('delete comments'))) {
            $comment->delete();
            // Changed from emit to dispatch with message
            $this->dispatch('commentAdded')->self();
            $this->dispatch('notify', ['message' => 'Comment deleted successfully!', 'type' => 'success']);
        }
    }

    public function deleteReply($replyId)
    {
        $reply = Replies::find($replyId);

        if ($reply && ($reply->user_id === Auth::id() || Auth::user()->can('delete replies'))) {
            $reply->delete();
            // Changed from emit to dispatch with message
            $this->dispatch('replyAdded')->self();
            $this->dispatch('notify', ['message' => 'Reply deleted successfully!', 'type' => 'success']);
        }
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }

    public function toggleSort()
    {
        $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
    }
}