<?php

namespace App\Repositories;

use App\Models\EcoEducationalPost;
use App\Models\User;

class EcoPostRepository
{
    protected $model;

    public function __construct(EcoEducationalPost $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->latest()->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        return $post;
    }

    public function delete($id)
    {
        $post = $this->find($id);
        return $post->delete();
    }

    public function getLeaderboard()
    {
        return User::withCount('ecoPosts')
            // 1. Sort by Points (Treat NULL as 0 so the order is correct)
            ->orderByRaw('COALESCE(points, 0) DESC')
            // 2. Tie-breaker: If points are equal, the one with more posts is ranked higher
            ->orderBy('eco_posts_count', 'desc')
            ->take(5)
            ->get();
        // Removed ->map() because we don't need to calculate a fake score anymore
    }
}
