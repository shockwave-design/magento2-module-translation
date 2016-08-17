<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Translation\Model\Framework;

/**
 * Translate library extended by external api translations
 *
 */
class Translate extends \Magento\Framework\Translate
{
    /** @var \Shockwavedesign\Translation\Model\Framework\App\Language\Dictionary */
    protected $packModuleDictionary;

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
        \Magento\Framework\App\Language\Dictionary $packDictionary,
        \Shockwavedesign\Translation\Model\Framework\App\Language\Dictionary $packModuleDictionary
    )
    {
        $this->packModuleDictionary = $packModuleDictionary;
        
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
     * @param null $area
     * @param bool|false $forceReload
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _loadPackModuleTranslation($area = null, $forceReload = false)
    {
        $currentModule = $this->getControllerModuleName();
        $allModulesExceptCurrent = array_diff($this->_moduleList->getNames(), [$currentModule]);

        $packagePaths = $this->packModuleDictionary->getLanguagePackPathes($this->getLocale());

        foreach ($packagePaths as $packagePath) {
            $this->loadPackModuleTranslationByModulesList($packagePath, $allModulesExceptCurrent);
            $this->loadPackModuleTranslationByModulesList($packagePath, [$currentModule]);
        }

        return $this;
    }

    /**
     * Load data from module translation files by list of modules
     *
     * @param array $modules
     * @return $this
     */
    protected function loadPackModuleTranslationByModulesList($packagePath, array $modules)
    {
        foreach ($modules as $module) {
            $moduleFilePath = $packagePath . DIRECTORY_SEPARATOR . $this->_getPackageModuleTranslationFile($module, $this->getLocale());
            $this->_addData($this->_getFileData($moduleFilePath));
        }
        return $this;
    }

    /**
     * Retrieve translation file for module
     *
     * @param string $moduleName
     * @param string $locale
     * @return string
     */
    protected function _getPackageModuleTranslationFile($moduleName, $locale)
    {
        $file = $this->_modulesReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_I18N_DIR, $moduleName);
        $filePath = basename(dirname(dirname($file))) . DIRECTORY_SEPARATOR . basename(dirname($file)) . DIRECTORY_SEPARATOR . basename($file);

        $filePath .= '/' . $locale . '.csv';
        return $filePath;
    }
}