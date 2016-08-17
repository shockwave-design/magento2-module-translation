<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Translate\Model\Framework;

/**
 * Translate library extended by external api translations
 *
 */
class Translate extends \Magento\Framework\Translate
{
    public function __construct(
        \Magento\Framework\View\DesignInterface $viewDesign,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\Module\ModuleList $moduleList,
        \Magento\Framework\Module\Dir\Reader $modulesReader,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\Translate\ResourceInterface $translate,
        \Magento\Framework\Locale\ResolverInterface $locale,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\File\Csv $csvParser,
        \Magento\Framework\App\Language\Dictionary $packDictionary
    )
    {
        
        
        parent::__construct($viewDesign, $cache, $viewFileSystem, $moduleList, $modulesReader, $scopeResolver, $translate, $locale, $appState, $filesystem, $request, $csvParser, $packDictionary);
    }


    /**
     * Initialize translation data
     *
     * @param string|null $area
     * @param bool $forceReload
     * @return $this
     */
    public function loadData($area = null, $forceReload = false)
    {
        parent::loadData($area, $forceReload);

        $this->_loadPackModuleTranslation($area, $forceReload);
        $this->_saveCache();

        return $this;
    }

    /**
     * @param null       $area
     * @param bool|false $forceReload
     *
     * @return $this
     */
    protected function _loadPackModuleTranslation($area = null, $forceReload = false)
    {
        $currentModule = $this->getControllerModuleName();
        $allModulesExceptCurrent = array_diff($this->_moduleList->getNames(), [$currentModule]);

        //$this->loadPackModuleTranslationByModulesList($allModulesExceptCurrent);
        //$this->loadPackModuleTranslationByModulesList([$currentModule]);

        return $this;
    }
}