<?php

use Illuminate\Support\Str;

return [
    'production' => false,
    'domain' => 'http://dekker.test',
    'siteName' => 'Claudio Dekker',
    'siteAuthor' => 'Claudio Dekker',
    'siteDescription' => 'Software Developer & Maintainer at InertiaJS',
    'type' => 'website',
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
