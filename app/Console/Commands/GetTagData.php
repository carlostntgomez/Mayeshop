<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;

class GetTagData extends Command
{
    protected $signature = 'get:tag-data';
    protected $description = 'Retrieves all tag data from the database.';

    public function handle()
    {
        $tags = Tag::all()->map(function($tag) {
            return ['id' => $tag->id, 'name' => $tag->name, 'type' => $tag->type];
        })->toArray();

        $this->info(json_encode($tags, JSON_PRETTY_PRINT));
    }
}
