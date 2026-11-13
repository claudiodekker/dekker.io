<?php

use App\TorchlightCodeBlocks;
use Illuminate\Support\Str;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use Torchlight\Engine\CommonMark\Extension as TorchlightExtension;
use Torchlight\Engine\Theme\Theme;

return [
    'domain' => 'http://dekker.test',
    'siteName' => 'Claudio Dekker',
    'siteAuthor' => 'Claudio Dekker',
    'siteDescription' => 'Software developer from Amsterdam.',
    'type' => 'website',
    'image' => '/assets/images/social.jpg',
    'commonmark' => [
        'config' => [
            'default_attributes' => [
                'strict_callables' => true,
                Image::class => ['loading' => 'lazy', 'decoding' => 'async'],
            ],
        ],
        'extensions' => [
            new AttributesExtension,
            new DefaultAttributesExtension,
            new StrikethroughExtension,
            new TableExtension,
            new TorchlightCodeBlocks(new TorchlightExtension([
                'dark' => Theme::fromFile(__DIR__.'/themes/dark.json'),
                'light' => Theme::fromFile(__DIR__.'/themes/light.json'),
            ])),
        ],
    ],
    'collections' => [
        'blog' => [
            'type' => 'article',
            'path' => 'blog/{filename}',
            'author' => '@claudiodekker',
            'categories' => ['articles'],
            'sort' => ['-date'],
            'filter' => function ($item) {
                return $item->published ?? true;
            },
        ]
    ],
    'getDate' => fn ($page) => DateTime::createFromFormat('U', $page->date),
    'styles' => fn () => file_get_contents(__DIR__.'/source/_assets/build/app.css'),
    'getDescription' => function ($page, $maxLength = 150) {
        if ($page->description) {
            return $page->description;
        }

        if ($page->excerpt) {
            return $page->excerpt;
        }

        if (method_exists($page, 'getContent')) {
            return Str::limit(str_replace(["\n", "\r"], ' ', strip_tags($page->getContent())), $maxLength);
        }

        return $page->siteDescription;
    },
    'getExcerpt' => function ($page, $length = 255) {
        if ($page->excerpt) {
            return $page->excerpt;
        }

        $content = preg_split('/<!--more-->/m', $page->getContent(), 2);
        $cleaned = trim(
            strip_tags(
                preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content[0]),
                '<code>'
            )
        );

        if (count($content) > 1) {
            return $cleaned;
        }

        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated) . ' [...]'
            : $cleaned;
    },
];
