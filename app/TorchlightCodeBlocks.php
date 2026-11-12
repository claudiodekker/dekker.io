<?php

namespace App;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Torchlight\Engine\CommonMark\Extension;

class TorchlightCodeBlocks implements ExtensionInterface, NodeRendererInterface
{
    /**
     * The Blade escapes Jigsaw adds to a Markdown file before it is parsed.
     *
     * @var array<string, string>
     */
    protected const JIGSAW_ESCAPES = [
        "{{'@'}}" => '@',
        '@{{' => '{{',
        '@{!!' => '{!!',
        "<{{'?php'}}" => '<?php',
    ];

    /**
     * The characters Blade would act on, as the HTML entities it leaves alone.
     *
     * @var array<string, string>
     */
    protected const BLADE_SAFE = [
        '@' => '&#64;',
        '{' => '&#123;',
    ];

    /**
     * Create a new extension instance.
     */
    public function __construct(protected Extension $torchlight)
    {
        //
    }

    /**
     * Register the extension as the renderer of fenced code blocks.
     */
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(FencedCode::class, $this, 100);
    }

    /**
     * Highlight the code as it was written, then make the result safe to compile as Blade.
     *
     * @param  \League\CommonMark\Extension\CommonMark\Node\Block\FencedCode  $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        $node->setLiteral(strtr($node->getLiteral(), static::JIGSAW_ESCAPES));

        $highlighted = $this->torchlight->renderer()->render($node, $childRenderer);

        return strtr((string) $highlighted, static::BLADE_SAFE);
    }
}
