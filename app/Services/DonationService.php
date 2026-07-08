<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Segment;
use App\Repositories\DonationRepository;
use Illuminate\Http\UploadedFile;

class DonationService
{
    protected $donationRepository;

    public function __construct(
        DonationRepository $donationRepository,
        private readonly FileStorageService $files
    ) {
        $this->donationRepository = $donationRepository;
    }

    public function getAllDonations()
    {
        return $this->donationRepository->all();
    }

    public function getDonationById($id)
    {
        return $this->donationRepository->find($id);
    }

    public function createDonation(array $data, ?array $images = null)
    {
        $donation = $this->donationRepository->create($data);

        foreach ($this->files->uploadPublicMany($images, 'donation_images') as $path) {
            $donation->donationImages()->create([
                'image' => $path,
            ]);
        }

        return $donation;
    }

    public function updateDonation(Donation $donation, array $data, ?array $images = null, ?array $deleteGalleryIds = null)
    {
        if (! empty($images['main']) && $images['main'] instanceof UploadedFile) {
            $data['image'] = $this->files->replacePublic($donation->image, $images['main'], 'donation_images');
        }

        if (! empty($deleteGalleryIds)) {
            $imagesToDelete = $donation->donationImages()->whereIn('id', $deleteGalleryIds)->get();
            $this->files->deleteManyIfExists($imagesToDelete->pluck('image'));
            $donation->donationImages()->whereIn('id', $deleteGalleryIds)->delete();
        }

        if (! empty($images['gallery'])) {
            $currentCount = $donation->donationImages()->count();
            $remainingSlots = max(0, 8 - $currentCount);
            $filesToUpload = array_slice($images['gallery'], 0, $remainingSlots);

            foreach ($this->files->uploadPublicMany($filesToUpload, 'donations_images') as $path) {
                $donation->donationImages()->create(['image' => $path]);
            }
        }

        if (! empty($images['proof']) && $images['proof'] instanceof UploadedFile) {
            $data['proof'] = $this->files->replacePublic($donation->proof, $images['proof'], 'donation_proofs');
        }

        return $this->donationRepository->update($donation, $data);
    }

    public function deleteDonation($donation)
    {
        $donation->loadMissing('donationImages');

        $this->files->deleteIfExists($donation->image);
        $this->files->deleteIfExists($donation->proof);
        $this->files->deleteManyIfExists($donation->donationImages->pluck('image'));

        return $this->donationRepository->delete($donation);
    }

    public function getApprovedDonationsBySegment(Segment $segment, ?int $categoryId = null)
    {
        return $this->donationRepository->getApprovedDonationsBySegement($segment, $categoryId);
    }

    public function getApprovedDonations()
    {
        return $this->donationRepository->all();
    }

    public function getDonationsByStatusPaginated(string $status, int $perPage = 10, string $pageName = 'page')
    {
        return $this->donationRepository->getByStatusPaginated($status, $perPage, $pageName);
    }

    public function getDonationsByVerificationStatusPaginated(string $status, int $perPage = 10, string $pageName = 'page')
    {
        return $this->donationRepository->getByVerificationStatusPaginated($status, $perPage, $pageName);
    }

    public function getDonationsByUser($userId)
    {
        return $this->donationRepository->getByUser($userId);
    }

    public function getMoreDonationsByUser($userId, $excludeDonationId, $limit = 6)
    {
        return $this->donationRepository->getMoreByUser($userId, $excludeDonationId, $limit);
    }
}
