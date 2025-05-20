<?php

namespace App\Models;

use App\Enum\ListingImageType;
use App\Utils\StringUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Multimedia extends Model
{
    protected $table = 'multimedias';
    protected $fillable = [
        'link',
        'filename',
        'description',
        'is_default', // 1: default, 0: not default
        'multimedia_type_id',
        'room_id',
        'listing_id'
    ];

    const TYPE_IMAGEN = [
        'ThumbnaiImageURL',
        'LargeImageURL',
        'ExtraLargeImageURL'
    ];
    const IMAGEN_DEFAULT = 'WebImageURL';

    protected $appends = [
        'url',
        'thumbnai_url',
        'large_url',
        'extra_large_url'
    ];

    public function getUrlAttribute()
    {
        return Storage::url($this->link);
    }

    public function getThumbnaiUrlAttribute()
    {
        return Storage::url(
            StringUtil::addFolderToFilePath($this->link, ListingImageType::THUMBNAIL->value)
        );
    }

    public function getLargeUrlAttribute()
    {
        return Storage::url(
            StringUtil::addFolderToFilePath($this->link, ListingImageType::LARGE->value)
        );
    }

    public function getExtraLargeUrlAttribute()
    {
        return Storage::url(
            StringUtil::addFolderToFilePath($this->link, ListingImageType::EXTRA_LARGE->value)
        );
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
    public function multimedia_type(): BelongsTo
    {
        return $this->belongsTo(MultimediaType::class);
    }
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}
