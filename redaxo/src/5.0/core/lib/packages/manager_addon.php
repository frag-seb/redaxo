<?php

class rex_addonManager extends rex_packageManager
{
  function __construct()
  {
    parent::__construct('addon_');
  }

  public function delete($addonName)
  {
    global $REX;

    // System AddOns dürfen nicht gelöscht werden!
    if(in_array($addonName, $REX['SYSTEM_PACKAGES']))
      return rex_i18n::msg('addon_systemaddon_delete_not_allowed');

    return parent::delete($addonName);
  }

  protected function includeConfig($addonName, $configFile)
  {
    global $REX; // Nötig damit im Addon verfügbar
    require $configFile;
  }


  protected function includeInstaller($addonName, $installFile)
  {
    global $REX; // Nötig damit im Addon verfügbar
    require $installFile;
  }

  protected function includeUninstaller($addonName, $uninstallFile)
  {
    global $REX; // Nötig damit im Addon verfügbar
    require $uninstallFile;
  }

  protected function apiCall($method, array $arguments)
  {
    return rex_call_func(array('rex_ooAddon', $method), $arguments, false);
  }

  protected function loadPackageInfos($addonName)
  {
    return self::loadPackage($addonName);
  }

  protected function baseFolder($addonName)
  {
    return rex_path::addon($addonName);
  }

  protected function assetsFolder($addonName)
  {
    return rex_path::addonAssets($addonName);
  }

  protected function dataFolder($addonName)
  {
    return rex_path::addonData($addonName);
  }

  protected function package($addonName)
  {
    return $addonName;
  }

  protected function configNamespace($addonName)
  {
    return $addonName;
  }

  /**
   * Loads the package.yml into $REX
   *
   * @param string $addonName The name of the addon
   */
  static public function loadPackage($addonName)
  {
    $package_file = rex_path::addon($addonName, 'package.yml');

    if(is_readable($package_file))
    {
      $ymlConfig = rex_file::getConfig($package_file);
      if($ymlConfig)
      {
        foreach($ymlConfig as $addonConfig)
        {
          foreach($addonConfig as $confName => $confValue)
          {
            rex_ooAddon::setProperty($addonName, $confName, rex_i18n::translateArray($confValue));
          }
        }
      }
    }
  }

  /**
   * Checks whether the requirements are met.
   *
   * @param string $addonName The name of the addon
   */
  protected function checkRequirements($addonName)
  {
    $state = parent::checkRequirements($addonName);

    if($state !== true)
      return $state;

    $pluginManager = new rex_pluginManager($addonName);
    foreach(rex_ooPlugin::getRegisteredPlugins($addonName) as $plugin)
    {
      // do not use rex_ooPlugin::isAvailable() here, because parent addon isn't activated
      if(rex_ooPlugin::getProperty($addonName, $plugin, 'status', false))
      {
        $pluginManager->loadPackageInfos($plugin);
        $return = $pluginManager->checkRequirements($plugin);
        if(is_string($return) && !empty($return))
        {
          $pluginManager->deactivate($plugin);
        }
      }
    }

    return $state;
  }

  /**
   * Checks if another Addon/Plugin which is activated, depends on the given addon
   *
   * @param string $addonName The name of the addon
   */
  protected function checkDependencies($addonName)
  {
    global $REX;

    $i18nPrefix = 'addon_dependencies_error_';
    $state = array();

    foreach(rex_ooAddon::getAvailableAddons() as $availAddonName)
    {
      $requirements = rex_ooAddon::getProperty($availAddonName, 'requires', array());
      if(isset($requirements['addons']) && is_array($requirements['addons']))
      {
        foreach($requirements['addons'] as $depName => $depAttr)
        {
          if($depName == $addonName)
          {
            $state[] = rex_i18n::msg($i18nPrefix .'addon', $availAddonName);
          }
        }
      }

      // check if another Plugin which is installed, depends on the addon being un-installed
      foreach(rex_ooPlugin::getAvailablePlugins($availAddonName) as $availPluginName)
      {
        $requirements = rex_ooPlugin::getProperty($availAddonName, $availPluginName, 'requires', array());
        if(isset($requirements['addons']) && is_array($requirements['addons']))
        {
          foreach($requirements['addons'] as $depName => $depAttr)
          {
            if($depName == $addonName)
            {
              $state[] = rex_i18n::msg($i18nPrefix .'plugin', $availAddonName, $availPluginName);
            }
          }
        }
      }
    }

    return empty($state) ? true : implode('<br />', $state);
  }

	/**
   * Adds the package to the package order
   *
   * @param string $addonName The name of the addon
   */
  protected function addToPackageOrder($addonName)
  {
    parent::addToPackageOrder($addonName);

    $pluginManager = new rex_pluginManager($addonName);
    foreach(rex_ooPlugin::getAvailablePlugins($addonName) as $plugin)
    {
      $pluginManager->addToPackageOrder($plugin);
    }
  }

  /**
   * Removes the package from the package order
   *
   * @param string $addonName The name of the addon
   */
  protected function removeFromPackageOrder($addonName)
  {
    parent::removeFromPackageOrder($addonName);

    $pluginManager = new rex_pluginManager($addonName);
    foreach(rex_ooPlugin::getRegisteredPlugins($addonName) as $plugin)
    {
      $pluginManager->removeFromPackageOrder($plugin);
    }
  }
}