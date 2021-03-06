<?php

/**
* This file is part of the OpenPNE package.
* (c) OpenPNE Project (http://www.openpne.jp/)
*
* For the full copyright and license information, please view the LICENSE
* file and the NOTICE file that were distributed with this source code.
*/

/**
* search for assets of the theme's files
*
* @package OpenPNE
* @subpackage theme
* @author suzuki_mar <supasu145@gmail.com>
*/
class opThemeAssetSearch extends opInstalledPluginManager
{
  private $webPath;
  private $themePath;

  /**
   * @todo raise an exception if there is no required parameters
   */
  public function __construct(array $params)
  {
    $this->webPath = $params['web_path'];
    $this->themePath = $params['theme_path'];
  }

  public function existsAssetsByThemeName($themeName)
  {
    if ($themeName === null) {
      return false;
    }

    $themeName = $this->getWebDir().'/'.$themeName;
    return file_exists($themeName);
  }

  /**
   * Find theme alternative if you can not use the selected theme
   */
  public function findSubstitutionTheme()
  {
    $pattern = $this->getWebDir().'/*';

    foreach (glob($pattern, GLOB_ONLYDIR) as $dirPath)
    {
      return str_replace($this->getWebDir(), '', $dirPath);
    }
  }

  public function loadThemeInstance()
  {
    $pattern = $this->getThemePath().'/*';

    $availableThemeNames = array();
    foreach (glob($pattern, GLOB_ONLYDIR) as $dirPath)
    {
      //it is not treated as a theme directory that there is no main.css
      $mainCssPath = $dirPath.'/css/main.css';
      if (file_exists($mainCssPath))
      {
        $availableThemeNames[] = pathinfo($dirPath, PATHINFO_FILENAME);
      }
    }

    $themes = array();
    foreach ($availableThemeNames as $name)
    {
      $themes[$name] = opTheme::getInstance($name);
    }

    return $themes;
  }

  public function findAssetsPathByThemeNameAndType($themeName, $type)
  {
    $pattern = $this->getWebDir().'/'.$themeName.'/'.$type.'/'.'*.'.$type;

    $files = array();
    foreach (glob($pattern) as $fileName)
    {
      $files[] = str_replace($this->getWebDir(), '/opSkinThemePlugin', $fileName);
    }

    if (empty($files))
    {
      return false;
    }

    return $files;
  }

  public function getThemePath()
  {
    return $this->themePath;
  }

  public function getWebDir()
  {
    return $this->webPath;
  }
}