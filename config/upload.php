<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Image Upload Size (KB)
    |--------------------------------------------------------------------------
    |
    | Laravel validation "max" rule for images is in kilobytes.
    | Default: 51200 KB = 50 MB per image.
    |
    */
    'max_image_kb' => (int) env('MAX_IMAGE_UPLOAD_KB', 51200),
];
