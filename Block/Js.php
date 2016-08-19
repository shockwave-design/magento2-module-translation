<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Shockwavedesign\Translation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Translation\Model\Js\Config;

class Js extends \Magento\Translation\Block\Js
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Translation\Model\FileManager
     */
    private $fileManager;

    /**
     * Is js translation set to dictionary mode
     *
     * @return bool
     */
    public function dictionaryEnabled()
    {
        return $this->config->dictionaryEnabled();
    }

    /**
     * gets current js-translation.json timestamp
     *
     * @return string
     */
    public function getTranslationFileTimestamp()
    {
        $debugTranslationHintsCookie = isset($_COOKIE['debugTranslationHints']) && $_COOKIE['debugTranslationHints'] === 'true';
        if($debugTranslationHintsCookie) {
            return random_int(0, time());
        }

        return $this->fileManager->getTranslationFileTimestamp();
    }
}
