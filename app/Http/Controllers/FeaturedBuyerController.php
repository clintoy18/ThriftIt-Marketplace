<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeaturedBuyerRequest;
use App\Services\FeaturedBuyerService;
use App\Repositories\FeaturedBuyerRepository;
use App\Models\FeaturedBuyer;
use App\Models\User;

class FeaturedBuyerController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(FeaturedBuyerService $service, FeaturedBuyerRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function store(StoreFeaturedBuyerRequest $request)
    {
        $this->service->store($request->validated(), $this->repository);
        return redirect()->back()->with('success', 'Featured Buyer added successfully!');
    }

    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $featuredBuyers = FeaturedBuyer::with('items')
            ->where('user_id', $user->id)
            ->get();

        return view('profile.show', compact('user', 'featuredBuyers'));
    }
}
