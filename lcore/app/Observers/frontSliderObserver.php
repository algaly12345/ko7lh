<?php

namespace App\Observers;

use App\Media;
use Illuminate\Support\Facades\File;

class frontSliderObserver
{
    /**
     * Handle the media "created" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function created(Media $media)
    {
        //
    }

    /**
     * Handle the media "updated" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function updated(Media $media)
    {
        //
    }

    /**
     * Handle the media "deleted" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        if(!is_null($media->getRawOriginal('image')))
        {
            $path = public_path('user-uploads/sliders/'.$media->getRawOriginal('image'));
            if($path){
                File::delete($path);
            }
        }
    }

    /**
     * Handle the media "restored" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function restored(Media $media)
    {
        //
    }

    /**
     * Handle the media "force deleted" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function forceDeleted(Media $media)
    {
        //
    }
}
