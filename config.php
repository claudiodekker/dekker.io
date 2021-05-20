<?php

return [
    'production' => false,
    'domain' => 'http://dekker.test',
    'basename' => 'Claudio Dekker',
    'author' => '@claudiodekker',
    'type' => 'website',
    'title' => 'Software Developer at Laravel & Maintainer at InertiaJS',
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
