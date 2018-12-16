<?php

/**
 * This file is part of the Terrific Twig package.
 *
 * (c) Robert Vogt <robert.vogt@namics.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deniaz\Terrific\Twig\TokenParser;

use Deniaz\Terrific\Provider\ContextProviderInterface;
use Deniaz\Terrific\Twig\Node\ComponentNode;
use Twig_Token;
use Twig_TokenParser;

/**
 * Includes a Terrific Component.
 *
 * Class ComponentTokenParser
 * @package Deniaz\Terrific\Twig\TokenParser
 */
final class ComponentTokenParser extends Twig_TokenParser
{
    /**
     * @var ContextProviderInterface Context Variable Provider.
     */
    private $ctxProvider;

    /**
     * ComponentTokenParser constructor.
     * @param ContextProviderInterface $ctxProvider
     */
    public function __construct(ContextProviderInterface $ctxProvider)
    {
        $this->ctxProvider = $ctxProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Twig_Token $token)
    {
        $component = $this->parser->getExpressionParser()->parseExpression();
        list($data, $only) = $this->parseArguments();

        return new ComponentNode($component, $this->ctxProvider, $data, $only, $token->getLine(), $this->getTag());
    }

    /**
     * Tokenizes the component stream.
     * @return array
     */
    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $data = null;
        $only = false;

        if ($stream->test(Twig_Token::BLOCK_END_TYPE)) {
            $stream->expect(Twig_Token::BLOCK_END_TYPE);

            return [$data, $only];
        }

        if ($stream->test(Twig_Token::NAME_TYPE, 'only')) {
            $only = true;
            $stream->next();
            $stream->expect(Twig_Token::BLOCK_END_TYPE);

            return [$data, $only];
        }

        $data = $this->parser->getExpressionParser()->parseExpression();

        if ($stream->test(Twig_Token::NAME_TYPE, 'only')) {
            $only = true;
            $stream->next();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return [$data, $only];
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'view';
    }
}
