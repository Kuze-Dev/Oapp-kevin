<div class="comment-section">
    <h3 class="text-xl font-semibold mb-4">Comments</h3>

    <div class="flex justify-between items-center mb-4">
        <div class="text-sm text-gray-600">
            {{ $comments->total() }} {{ Str::plural('comment', $comments->total()) }}
        </div>

        <button
            wire:click="toggleSort"
            class="text-sm text-blue-600 hover:underline flex items-center"
        >
            Sort: {{ $sortDirection === 'desc' ? 'Newest first' : 'Oldest first' }}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
        </button>
    </div>

    @auth
        <div class="mb-6">
            <form wire:submit.prevent="addComment">
                <div class="mb-2">
                    <textarea
                        wire:model.defer="commentText"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Add a comment..."
                        rows="3"
                    ></textarea>
                    @error('commentText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="addComment">Post Comment</span>
                        <span wire:loading wire:target="addComment">Posting...</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mb-6 p-4 bg-gray-100 rounded-lg text-center">
            <p>Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">login</a> to post a comment.</p>
        </div>
    @endauth

    <div class="space-y-6" wire:loading.class.delay="opacity-50">
        @forelse ($comments as $comment)
            <div class="bg-white p-4 rounded-lg shadow" id="comment-{{ $comment->id }}">
                <div class="flex justify-between mb-2">
                    <div class="flex items-center">
                        <div class="font-semibold">{{ $comment->user->name }}</div>
                        <div class="text-gray-500 text-sm ml-2">
                            {{ $comment->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->can('delete comments')))
                        <button
                            wire:click="deleteComment({{ $comment->id }})"
                            class="text-gray-400 hover:text-red-500"
                            onclick="return confirm('Are you sure you want to delete this comment?')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="mb-3">
                    {{ $comment->body }}
                </div>

                @auth
                    @if($replyingTo === $comment->id)
                        <div class="ml-6 mb-3">
                            <form wire:submit.prevent="addReply({{ $comment->id }})">
                                <div class="mb-2">
                                    <textarea
                                        wire:model.defer="replyText"
                                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Write a reply..."
                                        rows="2"
                                    ></textarea>
                                    @error('replyText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex space-x-2 justify-end">
                                    <button
                                        type="button"
                                        wire:click="cancelReply"
                                        class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                        wire:loading.attr="disabled"
                                    >
                                        <span wire:loading.remove wire:target="addReply">Reply</span>
                                        <span wire:loading wire:target="addReply">Sending...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <button
                            wire:click="startReply({{ $comment->id }})"
                            class="text-blue-600 hover:underline text-sm"
                        >
                            Reply
                        </button>
                    @endif
                @endauth

                @if($comment->replies->count() > 0)
                    <div class="mt-4 ml-6 space-y-3">
                        @foreach($comment->replies as $reply)
                            <div class="bg-gray-50 p-3 rounded" id="reply-{{ $reply->id }}">
                                <div class="flex justify-between mb-1">
                                    <div class="flex items-center">
                                        <div class="font-semibold">{{ $reply->user->name }}</div>
                                        <div class="text-gray-500 text-sm ml-2">
                                            {{ $reply->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    @if(Auth::check() && (Auth::id() === $reply->user_id || Auth::user()->can('delete replies')))
                                        <button
                                            wire:click="deleteReply({{ $reply->id }})"
                                            class="text-gray-400 hover:text-red-500"
                                            onclick="return confirm('Are you sure you want to delete this reply?')"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <div>
                                    {{ $reply->body }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-6 text-gray-500">
                No comments yet. Be the first to comment!
            </div>
        @endforelse

        @if($comments->hasMorePages())
            <div class="flex justify-center mt-4">
                <button
                    wire:click="loadMore"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="loadMore">Load More</span>
                    <span wire:loading wire:target="loadMore">Loading...</span>
                </button>
            </div>
        @endif
    </div>
</div>