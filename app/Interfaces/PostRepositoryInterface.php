<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface PostRepositoryInterface
{
    public function show($profileSlug, $postSlug);
    public function create();
    public function store(Request $request);
    public function delete(int $postId, int $authId);
}