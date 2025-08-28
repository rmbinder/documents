<?php
/**
 ***********************************************************************************************
 * Installation routine for the Admidio plugin Documents
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 * Parameters:  none
 *
 ***********************************************************************************************
 */

use Admidio\Infrastructure\Utils\FileSystemUtils;
use Plugins\Documents\classes\Config\ConfigTable;

try {
    require_once(__DIR__ . '/../../../system/common.php');
    require_once(__DIR__ . '/common_function.php');
    
    // only administrators are allowed to start this module
    if (!$gCurrentUser->isAdministrator())          
    {
        //throw new Exception('SYS_NO_RIGHTS');                     // über Exception wird nur SYS_NO_RIGHTS angezeigt
        $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
    }
    else
    {
        $zeilenumbruch = "\r\n";
        
        // ADMIDIO_URL auch möglich, aber dann wird 'allow_url_open' (PHP.ini) benötigt
        $templateFile = ADMIDIO_PATH . FOLDER_THEMES . '/simple/templates/modules/profile.view';         
        try
        {
            if (!file_exists($templateFile.'_documents_save.tpl'))
            {
                FileSystemUtils::copyFile($templateFile.'.tpl', $templateFile.'_documents_save.tpl');
                
                $templateString = file_get_contents($templateFile.'.tpl');
             
                $substArray = array(
                    '{if $showRelations}'               =>  '{include file="../../../../adm_plugins/Documents/templates/profile.view.include.button.plugin.documents.tpl"}',
                    '<!-- User Relations Tab -->'       =>  '{include file="../../../../adm_plugins/Documents/templates/profile.view.include.documents.tab.plugin.documents.tpl"}',
                    '<!-- User Relations Accordion -->' =>  '{include file="../../../../adm_plugins/Documents/templates/profile.view.include.documents.accordion.plugin.documents.tpl"}'
                );
                
                foreach ($substArray as $needle => $subst)
                {
                    $templateString = substr_replace($templateString, $subst.$zeilenumbruch, strpos($templateString, $needle), 0);
                }
                file_put_contents($templateFile.'.tpl', $templateString);
            }
            else 
            {
                // es gibt bereits eine save-Datei, d.h. die Änderungen sind bereits eingetragen; irgendein 'Superchecker' führt die Install-Routine ein zweites mal aus
            }
        }
        catch (\RuntimeException $exception)
        {
            $gMessage->show($exception->getMessage());
            // => EXIT
        }
        catch (\UnexpectedValueException $exception)
        {
            $gMessage->show($exception->getMessage());
            // => EXIT
        }
        
        $profileFile = ADMIDIO_PATH . FOLDER_MODULES . '/profile/profile';  
        try
        {
            if (!file_exists($profileFile.'_documents_save.php'))
            {
                FileSystemUtils::copyFile($profileFile.'.php',$profileFile.'_documents_save.php');
                
                $profileString = file_get_contents($profileFile.'.php');

                $needle = '$page->show();';
                $subst = "require_once(ADMIDIO_PATH . FOLDER_PLUGINS .'/Documents/system/documents.php');";
                
                $profileString = substr_replace($profileString, $subst.$zeilenumbruch, strpos($profileString, $needle), 0);
                file_put_contents($profileFile.'.php', $profileString);
            }
            else
            {
                // es gibt bereits eine save-Datei, d.h. die Änderungen sind bereits eingetragen; irgendein 'Superchecker' führt die Install-Routine ein zweites mal aus
            }
        }
        catch (\RuntimeException $exception)
        {
            $gMessage->show($exception->getMessage());
            // => EXIT
        }
        catch (\UnexpectedValueException $exception)
        {
            $gMessage->show($exception->getMessage());
            // => EXIT
        }
      
        //  die Konfigurationsdaten bearbeiten
        
        // eine neues Objekt erzeugen
        $pPreferences = new ConfigTable();
        
        // prüfen, ob die Konfigurationstabelle bereits vorhanden ist und ggf. neu anlegen oder aktualisieren
        if ($pPreferences->checkforupdate())
        {
            $pPreferences->init();
        }
        
        $pPreferences->save();
        
        $gMessage->show($gL10n->get('PLG_DOCUMENTS_INSTALLMESSAGE'));
    }
    
} catch (Exception $e) {
    $gMessage->show($e->getMessage());
}


