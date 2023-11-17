<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post
{
    public string $title;
    public string $slug;
    public string $excerpt;
    public string $date;
    public string $body;

    /**
     * @param string $title
     * @param string $slug
     * @param string $excerpt
     * @param string $date
     * @param string $body
     */
    public function __construct(string $title, string $slug, string $excerpt, string $date, string $body)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
    }

    public static function all()
    {
        return cache()->rememberForever('posts.all',function(){
        return $posts = collect(File::files(resource_path("posts")))
            ->map(fn($file) => YamlFrontMatter::parseFile($file))
            ->map(fn($document) => new Post(
                $document->title,
                $document->slug,
                $document->excerpt,
                $document->date,
                $document->body()
            ))
            ->sortByDesc('date');
        });
    }
    public static function find($slug)
    {
        return static::all()->firstWhere('slug',$slug);
    }

}
