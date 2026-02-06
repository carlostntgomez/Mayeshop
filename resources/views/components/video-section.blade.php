<div class="ul-container">
    @if ($video)
        <div class="ul-video">
            <div>
                <img src="{{ asset('storage/' . $video->image_path) }}" alt="Video Banner" class="ul-video-cover">
            </div>
            <a href="{{ $video->video_url }}" class="ul-video-btn"><i class="fas fa-play"></i></a>
        </div>
    @endif
</div>