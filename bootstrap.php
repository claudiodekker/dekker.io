<?php

use Symfony\Component\Process\Process;
use TightenCo\Jigsaw\Jigsaw;

/** @var $container \Illuminate\Container\Container */
/** @var $events \TightenCo\Jigsaw\Events\EventBus */

/**
 * You can run custom code at different stages of the build process by
 * listening to the 'beforeBuild', 'afterCollections', and 'afterBuild' events.
 *
 * For example:
 *
 * $events->beforeBuild(function (Jigsaw $jigsaw) {
 *     // Your code here
 * });
 */
$container['markdownParser']->code_block_content_func = function ($code, $language) {
    /**
     * A list of Jigsaw-made Blade escapes.
     * @see \TightenCo\Jigsaw\Handlers\MarkdownHandler::getEscapedMarkdownContent()
     */
    $escapedTags = [
        '@' => "{{'@'}}",
        '{{' => '@{{',
        '{!!' => '@{!!',
        '<?php' => "<{{'?php'}}"
    ];

    /**
     * First, we'll unescape any of the escapes that were made by Jigsaw.
     */
    $unescaped = strtr($code, array_flip($escapedTags));

    /**
     * Next, we'll ask Prism.js to sprinkle in some syntax highlighting.
     */
    $process = new Process(['node', 'prism.js', $language ?: 'plaintext']);
    $process->setInput($unescaped);
    $process->run();

    if ($errorOutput = $process->getErrorOutput()) {
        dump($errorOutput);
    }

    /**
     * Finally, we'll properly re-apply any of Jigsaw's escapes
     */
    return strtr($process->getOutput(), [
        '@' => "{{'@'}}",
        '{{' => "{{'{{'}}",
        '{!!' => "{{'{!!'}}",
        '<?php' => "{{'<?php'}}",
    ]);
};
