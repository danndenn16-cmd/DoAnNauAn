<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.max'      => 'Bình luận tối đa 1000 ký tự.',
        ]);

        $recipe->comments()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);

        return back()->with('success', 'Đã gửi bình luận.');
    }

    public function destroy(Comment $comment)
    {
        // Chỉ chủ bình luận hoặc admin được xóa
        abort_unless(
            auth()->id() === $comment->user_id || auth()->user()->isStaff(),
            403
        );

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}