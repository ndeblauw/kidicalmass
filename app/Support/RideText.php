<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Turns the two kinds of participant text on a ride page into renderable pieces.
 *
 * - The fixed "Goed om te weten" text: one bullet per line, optionally "Label: text".
 *   It lives in a lang key for now (a draft awaiting the client's bullets) and will
 *   become an admin textarea later, so it is parsed from a plain multi-line string.
 * - The per-activity free text (`extra_info`): markdown-lite from a plain admin
 *   textarea. Paragraphs, line breaks and links work; raw HTML, headings and images
 *   never reach the page.
 */
final class RideText
{
    private static ?MarkdownConverter $converter = null;

    /**
     * Split the fixed multi-line text into list items. Never returns HTML: the view
     * escapes both parts.
     *
     * @return list<array{label: ?string, text: string}>
     */
    public static function goodToKnowItems(string $text): array
    {
        $items = [];

        foreach (preg_split('/\R/u', $text) as $line) {
            $line = preg_replace('/^[\s\x{00A0}\x{202F}]+|[\s\x{00A0}\x{202F}]+$/u', '', $line);
            $line = preg_replace('/^[-*\x{2022}\x{00B7}]\h+/u', '', $line);

            if ($line === '') {
                continue;
            }

            if (preg_match('/^(?<label>[^:]{1,40}?)\h*:\h+(?<text>\S.*)$/u', $line, $matches)) {
                $items[] = ['label' => trim($matches['label']), 'text' => trim($matches['text'])];

                continue;
            }

            $items[] = ['label' => null, 'text' => $line];
        }

        return array_values($items);
    }

    /**
     * Render the per-activity free text to sanitised HTML, or null when there is
     * nothing to show. Print the result with {{ }}, never {!! !!}.
     */
    public static function renderExtraInfo(?string $text): ?HtmlString
    {
        if (blank(trim($text ?? ''))) {
            return null;
        }

        $html = self::converter()->convert($text)->getContent();

        return new HtmlString(trim($html));
    }

    private static function converter(): MarkdownConverter
    {
        return self::$converter ??= new MarkdownConverter(self::environment());
    }

    private static function environment(): Environment
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 10,
            'renderer' => ['soft_break' => "<br>\n"],
            'external_link' => [
                'internal_hosts' => [parse_url((string) config('app.url'), PHP_URL_HOST)],
                'open_in_new_window' => true,
                'nofollow' => '',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new AutolinkExtension);
        $environment->addExtension(new ExternalLinkExtension);

        $environment->addRenderer(Heading::class, new class implements NodeRendererInterface
        {
            public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
            {
                return new HtmlElement('p', [], new HtmlElement('strong', [], $childRenderer->renderNodes($node->children())));
            }
        }, 10);

        $environment->addRenderer(Image::class, new class implements NodeRendererInterface
        {
            public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
            {
                return $childRenderer->renderNodes($node->children());
            }
        }, 10);

        return $environment;
    }
}
