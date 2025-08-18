<?php
/**
 ***********************************************************************************************
 * Delete file for the Admidio plugin documents
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 * Parameters:
 *
 * mode         : 1 - Security query before deletion
 *                2 - Delete file
 * folder_uuid  : UUID of the documents folder in the database
 * name         : Name of the file      
 ***********************************************************************************************
 */

use Admidio\Documents\Entity\File;
use Admidio\Infrastructure\Utils\SecurityUtils;

require_once(__DIR__ . '/../../system/common.php');

if(!defined('PLUGIN_FOLDER'))
{
    define('PLUGIN_FOLDER', '/'.substr(__DIR__,strrpos(__DIR__,DIRECTORY_SEPARATOR)+1));
}

// Initialize and check the parameters
$getMode       = admFuncVariableIsValid($_GET, 'mode', 'int', array('requireValue' => true, 'validValues' => array(1,2)));
$getFileUuid   = admFuncVariableIsValid($_GET, 'file_uuid', 'string');
$getName       = admFuncVariableIsValid($_GET, 'name', 'file');

// check if the module is enabled and disallow access if it's disabled
if (!$gSettingsManager->getBool('documents_files_module_enabled')) 
{
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
    // => EXIT
}

if (!$gCurrentUser->isAdministratorDocumentsFiles()) 
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
    // => EXIT
}

if ($getMode === 1)    
{
    // The class message says: @deprecated 4.3.0:4.4.0 "setForwardYesNo()" is deprecated, use "Message::setYesNoButton()" instead
    // However, the method setYesNoButton is missing the statement: $this->forwardMode = true;
    // $forwardMode: bool Set a flag if the given url should be used as a forward url. If set to no than a POST ajax call
    // I don't use ajax!!!!! setYesNoButton cannot be used
    // Alternative: HtmlForm with two SubmitButtons for Yes and No
    
    // $gMessage->setYesNoButton(SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/file_delete.php', array('mode' => 2, 'file_uuid' => $getFileUuid, 'name' => $getName )));
    $gMessage->setForwardYesNo(SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/file_delete.php', array('mode' => 2, 'file_uuid' => $getFileUuid, 'name' => $getName )));
    
    $gMessage->show($gL10n->get('PLG_DOCUMENTS_REALLY_DELETE', array($getName)), $gL10n->get('SYS_DELETE_FILE'));
}
elseif ($getMode === 2)       
{
    try 
    {
        if ($getFileUuid !== '')
        {
            // get recordset of current file from database
            $file = new File($gDb);
            $file->getFileForDownload($getFileUuid);
            
            if ($file->delete())
            {
                $gMessage->setForwardUrl($gNavigation->getUrl(), 2000);
                $gMessage->show($gL10n->get('PLG_DOCUMENTS_FILE_DELETED', array($getName)), $gL10n->get('SYS_NOTE'));
                // => EXIT
            }
        }
        else
        {
            // if no file id was set then show error
            $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
            // => EXIT
        }
    } 
    catch (AdmException $e) 
    {
        $e->showHtml();
        // => EXIT
    }
}
