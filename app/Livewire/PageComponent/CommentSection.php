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
    public $editingCommentId = null;
    public $editingReplyId = null;
    public $editCommentText = '';
    public $editReplyText = '';
    public $perPage = 5;
    public $sortDirection = 'desc';

    // Updated to use dispatchBrowserEvent instead of emit
    protected $listeners = ['commentAdded' => '$refresh', 'replyAdded' => '$refresh'];

    protected $rules = [
        'commentText' => 'required|min:3',
        'replyText' => 'required|min:3',
        'editCommentText' => 'required|min:3',
        'editReplyText' => 'required|min:3',
    ];

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function render()
    {
        $comments = Comments::where('product_id', $this->productId)
            ->with(['user', 'replies.user' , 'likes'])
            ->orderBy('created_at', $this->sortDirection)
            ->paginate($this->perPage);

            // dd($comments);

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

    public function startEditComment($commentId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $comment = Comments::find($commentId);

        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->can('edit comments'))) {
            $this->editingCommentId = $commentId;
            $this->editCommentText = $comment->body;
        }
    }

    public function cancelEditComment()
    {
        $this->editingCommentId = null;
        $this->editCommentText = '';
    }

    public function updateComment($commentId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'editCommentText' => 'required|min:3',
        ]);

        $comment = Comments::find($commentId);

        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->can('edit comments'))) {
            $comment->update([
                'body' => $this->editCommentText,
            ]);

            $this->editingCommentId = null;
            $this->editCommentText = '';
            $this->dispatch('commentAdded')->self();
            $this->dispatch('notify', ['message' => 'Comment updated successfully!', 'type' => 'success']);
        }
    }

    public function startEditReply($replyId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $reply = Replies::find($replyId);

        if ($reply && ($reply->user_id === Auth::id() || Auth::user()->can('edit replies'))) {
            $this->editingReplyId = $replyId;
            $this->editReplyText = $reply->body;
        }
    }

    public function cancelEditReply()
    {
        $this->editingReplyId = null;
        $this->editReplyText = '';
    }

    public function updateReply($replyId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'editReplyText' => 'required|min:3',
        ]);

        $reply = Replies::find($replyId);

        if ($reply && ($reply->user_id === Auth::id() || Auth::user()->can('edit replies'))) {
            $reply->update([
                'body' => $this->editReplyText,
            ]);

            $this->editingReplyId = null;
            $this->editReplyText = '';
            $this->dispatch('replyUpdated')->self();
            $this->dispatch('notify', ['message' => 'Reply updated successfully!', 'type' => 'success']);
        }
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

    public function likeComment($commentId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $comment = Comments::find($commentId);

        if (!$comment) {
            $this->dispatch('notify', ['message' => 'Comment not found.', 'type' => 'error']);
            return;
        }

        $userId = Auth::id();
        $existingLike = $comment->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $message = 'You unliked the comment!';
        } else {
            $comment->likes()->create(['user_id' => $userId]);
            $message = 'You liked the comment!';
        }

        $this->dispatch('commentUpdated')->self(); // changed to better naming
        $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
    }

    public function likeReply($replyId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $reply = Replies::find($replyId);

        if (!$reply) {
            $this->dispatch('notify', ['message' => 'Reply not found.', 'type' => 'error']);
            return;
        }

        $userId = Auth::id();
        $existingLike = $reply->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $message = 'You unliked the reply!';
        } else {
            $reply->likes()->create(['user_id' => $userId]);
            $message = 'You liked the reply!';
        }

        $this->dispatch('replyUpdated')->self(); // changed to better naming
        $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
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