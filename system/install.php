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
use Admidio\Infrastructure\Exception;
use Plugins\Documents\classes\Config\ConfigTable;

try {
    require_once (__DIR__ . '/../../../system/common.php');
    require_once (__DIR__ . '/common_function.php');

    // only administrators are allowed to start this module
    if (! $gCurrentUser->isAdministrator()) {
        throw new Exception('SYS_NO_RIGHTS');
    }

    // für die Anzeige von Documents-Daten inm Profil eines Mitglieds müssen original Admidio-Dateien geändert werden
   
    $zeilenumbruch = "\r\n";
    
    // ADMIDIO_URL auch möglich, aber dann wird 'allow_url_open' (PHP.ini) benötigt
    $templateFile = ADMIDIO_PATH . FOLDER_THEMES . '/simple/templates/modules/profile.view';
    try {
        if (! file_exists($templateFile . '_documents_save.tpl')) {
            //zur Sicherheit wird eine Kopie der Originaldatei erzeugt (bei der Deinstallation wird sie wieder gelöscht)
            FileSystemUtils::copyFile($templateFile . '.tpl', $templateFile . '_documents_save.tpl');

            // Template-Datei einlesen
            $templateString = file_get_contents($templateFile . '.tpl');

            // diese Texte in die profile.view.tpl einfügen ($needle => $subst)
            $substArray = array(
                '{if $showRelations}' => '{include file="../../../..' . FOLDER_PLUGINS . PLUGIN_FOLDER  .'/templates/profile.view.include.button.plugin.documents.tpl"}'.$zeilenumbruch,
                '<!-- User Relations Tab -->' => '{include file="../../../..' . FOLDER_PLUGINS . PLUGIN_FOLDER  .'/templates/profile.view.include.documents.tab.plugin.documents.tpl"}'.$zeilenumbruch,
                '<!-- User Relations Accordion -->' => '{include file="../../../..' . FOLDER_PLUGINS . PLUGIN_FOLDER  .'/templates/profile.view.include.documents.accordion.plugin.documents.tpl"}'.$zeilenumbruch
            );
            foreach ($substArray as $needle => $subst) {
                $templateString = substr_replace($templateString, $subst, strpos($templateString, $needle), 0);
            }
            
            // Template-Datei wieder schreiben
            file_put_contents($templateFile . '.tpl', $templateString);
        } else {
            // es gibt bereits eine save-Datei, d.h. die Änderungen sind bereits eingetragen; irgendein 'Superchecker' führt die Install-Routine ein zweites mal aus
        }
    } catch (\RuntimeException $exception) {
        $gMessage->show($exception->getMessage());
        // => EXIT
    } catch (\UnexpectedValueException $exception) {
        $gMessage->show($exception->getMessage());
        // => EXIT
    }

    $profileFile = ADMIDIO_PATH . FOLDER_MODULES . '/profile/profile';
    try {
        if (! file_exists($profileFile . '_documents_save.php')) {
            //zur Sicherheit wird eine Kopie der Originaldatei erzeugt (bei der Deinstallation wird sie wieder gelöscht)
            FileSystemUtils::copyFile($profileFile . '.php', $profileFile . '_documents_save.php');

            // PHP-Datei einlesen
            $profileString = file_get_contents($profileFile . '.php');

            // diesen Text in die profile.view.tpl einfügen
            $needle = '$page->show();';
            $subst = "require_once(ADMIDIO_PATH . FOLDER_PLUGINS .'" .PLUGIN_FOLDER . "/system/documents.php');";
            $profileString = substr_replace($profileString, $subst . $zeilenumbruch, strpos($profileString, $needle), 0);
            
            // PHP-Datei wieder schreiben
            file_put_contents($profileFile . '.php', $profileString);
        } else {
            // es gibt bereits eine save-Datei, d.h. die Änderungen sind bereits eingetragen; irgendein 'Superchecker' führt die Install-Routine ein zweites mal aus
        }
    } catch (\RuntimeException $exception) {
        $gMessage->show($exception->getMessage());
        // => EXIT
    } catch (\UnexpectedValueException $exception) {
        $gMessage->show($exception->getMessage());
        // => EXIT
    }

    // die Konfigurationsdaten bearbeiten

    // eine neues Objekt erzeugen
    $pPreferences = new ConfigTable();

    // prüfen, ob die Konfigurationstabelle bereits vorhanden ist und ggf. neu anlegen oder aktualisieren
    if ($pPreferences->checkforupdate()) {
        $pPreferences->init();
    }

    $pPreferences->save();

    $gMessage->show($gL10n->get('PLG_DOCUMENTS_INSTALLMESSAGE'));
} catch (Exception $e) {
    $gMessage->show($e->getMessage());
}


