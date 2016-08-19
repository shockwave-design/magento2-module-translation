<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Shockwavedesign\Translation\Model\Framework\Phrase\Renderer;

use Magento\Framework\Phrase\RendererInterface;
use Magento\Framework\TranslateInterface;
use Psr\Log\LoggerInterface;

class Translate extends \Magento\Framework\Phrase\Renderer\Translate
{
    /**
     * Render source text
     *
     * @param [] $source
     * @param [] $arguments
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(array $source, array $arguments)
    {
        $text = end($source);
        /* If phrase contains escaped quotes then use translation for phrase with non-escaped quote */
        $text = str_replace('\"', '"', $text);
        $text = str_replace("\\'", "'", $text);

        try {
            $data = $this->translator->getData();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }

        $result = array_key_exists($text, $data) ? $data[$text] : end($source);

        $debugTranslationHintsCookie = isset($_COOKIE['debugTranslationHints']) && $_COOKIE['debugTranslationHints'] === 'true';
        if($debugTranslationHintsCookie) {
            return '[' . end($source) . '|' . $result. ']';
        }

        return $result;
    }
}
