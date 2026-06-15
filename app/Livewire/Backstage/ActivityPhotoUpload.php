<?php

namespace App\Livewire\Backstage;

use App\Models\Activity;
use App\Models\Group;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ActivityPhotoUpload extends Component
{
    use WithFileUploads;

    public Activity $activity;

    public Group $group;

    public $mainPhoto;

    public array $galleryPhotos = [];

    public bool $uploading = false;

    public bool $isCaptain = false;

    public function mount(Group $group, Activity $activity): void
    {
        $this->group = $group;
        $this->activity = $activity->load('media');

        $this->authorizeAccess();

        $user = request()->user();
        $this->isCaptain = $user && ($user->isSuperAdmin() || $user->isCaptainOf($group));
    }

    public function updatedMainPhoto(): void
    {
        $this->authorizeAccess();

        $this->validate([
            'mainPhoto' => ['image', 'max:15360'],
        ]);

        $this->uploading = true;

        $this->activity
            ->clearMediaCollection('main')
            ->addMedia($this->mainPhoto->getRealPath())
            ->usingName($this->mainPhoto->getClientOriginalName())
            ->toMediaCollection('main');

        $this->mainPhoto = null;
        $this->uploading = false;
    }

    public function uploadGallery(): void
    {
        $this->authorizeAccess();

        $this->validate([
            'galleryPhotos.*' => ['image', 'max:15360'],
        ]);

        $this->uploading = true;

        foreach ($this->galleryPhotos as $photo) {
            $this->activity
                ->addMedia($photo->getRealPath())
                ->usingName($photo->getClientOriginalName())
                ->toMediaCollection('gallery');
        }

        $this->galleryPhotos = [];
        $this->uploading = false;
    }

    public function removeMain(): void
    {
        $this->authorizeCaptain();

        $this->activity->clearMediaCollection('main');
    }

    public function removeGalleryItem(int $mediaId): void
    {
        $this->authorizeCaptain();

        $media = Media::find($mediaId);

        if ($media && $media->model->is($this->activity)) {
            $media->delete();
        }
    }

    private function authorizeAccess(): void
    {
        abort_unless($this->activity->groups()->where('group_id', $this->group->id)->exists(), 404);
        abort_unless(request()->user()?->isPinkVestOf($this->group), 403);
    }

    private function authorizeCaptain(): void
    {
        abort_unless(request()->user()?->isCaptainOf($this->group), 403);
    }

    public function render()
    {
        return view('livewire.backstage.activity-photo-upload')
            ->layout('layouts.backstage', [
                'title' => 'Foto\'s uploaden — '.$this->activity->title_nl,
                'group' => $this->group,
                'volunteer' => request()->user(),
            ]);
    }
}
