<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'affair', 'date_task', 'reminder_date', 'listing', 'description', 'filetxt', 'priority_id', 'activity_listing_id'
    ];

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function activityListing()
    {
        return $this->belongsTo(ActivityListing::class, 'activity_listing_id');
    }
}
