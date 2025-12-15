<?php

namespace App\Services;

use App\Repositories\EcoPostRepository;
use Illuminate\Support\Facades\Storage;
use App\Models\EcoEducationalPost;
use App\Models\User;

class EcoPostService
{
    protected $repo;

    public function __construct(EcoPostRepository $repo)
    {
        $this->repo = $repo;
    }

    public function listPosts()
    {
        return $this->repo->all();
    }

    public function getPost($id)
    {
        return $this->repo->find($id);
    }
    public function createPost(array $data)
    {
        if (isset($data['image'])) {

            // Store file in S3 (e.g., eco_posts/filename.jpg)
            $path = $data['image']->store('eco_posts', [
                'disk' => 's3',
                'visibility' => 'public',
            ]);

            // Store only the S3 key/path in the database
            $data['image'] = $path;
        }

        return EcoEducationalPost::create($data);
    }

    public function getLeaderboard()
    {
       return $this->repo->getLeaderboard();
    }

    public function updatePost($id, array $data)
    {
        if (isset($data['image'])) {

            // Store new image in S3
            $path = $data['image']->store('eco_posts', [
                'disk' => 's3',
                'visibility' => 'public',
            ]);

            // Store only the S3 key/path in database
            $data['image'] = $path;
        }

        return $this->repo->update($id, $data);
    }


    public function deletePost($id)
    {
        return $this->repo->delete($id);
    }
}
