<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasProfilePicture
{
    /**
     * Update the user's profile photo.
     *
     * @param  \Illuminate\Http\UploadedFile  $picture
     * @return void
     */
    public function updateProfilePicture(UploadedFile $picture)
    {
        tap($this->profile_photo_path, function ($previous) use ($picture) {
            $this->forceFill([
                'profile_picture_path' => $picture->storePublicly(
                    'profile-pictures', ['disk' => $this->profilePictureDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePictureDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePicture()
    {
        Storage::disk($this->profilePictureDisk())->delete($this->profile_picture_path);

        $this->forceFill([
            'profile_picture_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture_path
                    ? Storage::disk($this->profilePictureDisk())->url($this->profile_picture_path)
                    : $this->defaultProfilePictureUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePictureUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that profile photos should be stored on.
     *
     * @return string
     */
    protected function profilePictureDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
