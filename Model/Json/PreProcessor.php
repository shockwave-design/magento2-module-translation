<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavedesign\Translation\Model\Json;

use Magento\Framework\View\Asset\PreProcessorInterface;
use Magento\Translation\Model\Js\Config;
use Magento\Translation\Model\Js\DataProviderInterface;
use Magento\Framework\View\Asset\PreProcessor\Chain;
use Magento\Framework\View\Asset\File\FallbackContext;
use Magento\Framework\App\AreaList;
use Magento\Framework\TranslateInterface;
use Psr\Log\LoggerInterface;

/**
 * PreProcessor responsible for providing js translation dictionary
 */
class PreProcessor extends \Magento\Translation\Model\Json\PreProcessor
{
    /**
     * Js translation configuration
     *
     * @var Config
     */
    protected $config;

    /**
     * Translation data provider
     *
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var AreaList
     */
    protected $areaList;

    /**
     * @var TranslateInterface
     */
    protected $translate;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Config $config
     * @param DataProviderInterface $dataProvider
     * @param AreaList $areaList
     * @param TranslateInterface $translate
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        DataProviderInterface $dataProvider,
        AreaList $areaList,
        TranslateInterface $translate,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->dataProvider = $dataProvider;
        $this->areaList = $areaList;
        $this->translate = $translate;
        $this->logger = $logger;
    }

    /**
     * Transform content and/or content type for the specified preprocessing chain object
     *
     * @param Chain $chain
     * @return void
     */
    public function process(Chain $chain)
    {
        if ($this->isDictionaryPath($chain->getTargetAssetPath())) {
            $context = $chain->getAsset()->getContext();

            $themePath = '*/*';
            $areaCode = \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;

            if ($context instanceof FallbackContext) {
                $themePath = $context->getThemePath();
                $areaCode = $context->getAreaCode();
                $this->translate->setLocale($context->getLocale());
            }

            $area = $this->areaList->getArea($areaCode);
            $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);

            $content = $this->dataProvider->getData($themePath);
            foreach ($content as $key => $translation) {
                if(!mb_check_encoding($key) || !mb_check_encoding($translation)) {
                    $this->logger->error(
                        __('Wrong translation: [%1] - [%2]', $key, $translation),
                        ['key' => $key, 'translation' => $translation]
                    );

                    unset($content[$key]);
                }
            }

            $jsonContent = json_encode($content);
            if($jsonContent === false) {
                $this->logger->error(__('Json encode of translations failed'), ['content' => $content]);
                throw new \Exception('Json encode of translations failed');
            }

            $chain->setContent($jsonContent);
            $chain->setContentType('json');
        }
    }

    /**
     * Is provided path the path to translation dictionary
     *
     * @param string $path
     * @return bool
     */
    protected function isDictionaryPath($path)
    {
        return (strpos($path, $this->config->getDictionaryFileName()) !== false);
    }
}
