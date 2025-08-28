<?php
/**
 ***********************************************************************************************
 * Main routine for the Admidio plugin Documents
 * 
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 ***********************************************************************************************
 */

use Admidio\Documents\Entity\Folder;
use Admidio\Infrastructure\Utils\SecurityUtils;
use Admidio\Infrastructure\Utils\StringUtils;
use Plugins\Documents\classes\Config\ConfigTable;

if (basename($_SERVER['SCRIPT_FILENAME']) === 'documents.php') {
    exit('This page may not be called directly!');
}

require_once(__DIR__ . '/../../../system/common.php');
require_once(__DIR__ . '/common_function.php');
//require_once(__DIR__ . '/config.php'); 

$pPreferences = new ConfigTable();
$pPreferences->read();

$documentsTemplateData = array();

$serialNumberOfMember = $user->getValue($pPreferences->config['settings']['serialNumberField']);

if ($pPreferences->config['settings']['maxPositions'] !== 0 && strlen($serialNumberOfMember) < $pPreferences->config['settings']['maxPositions'])
{
    $serialNumberOfMember = str_pad($serialNumberOfMember, $pPreferences->config['settings']['maxPositions'], '0', STR_PAD_LEFT );
}

// get recordset of documents folder from database
$documentsFolder = new Folder($gDb);              
$documentsFolder->readDataByUuid($pPreferences->config['settings']['folderUUID']);

// read all files in the documents folder
$documents = $documentsFolder->getFilesWithProperties();

foreach ($documents as $document)
{
    If ($pPreferences->config['settings']['separator'] !== '')
    {
        $serialNumberOfFile = substr($document['fil_name'], 0, strpos($document['fil_name'], $pPreferences->config['settings']['separator']));
    }
    else
    {
        $serialNumberOfFile =  $document['fil_name'];
    }
    
    if ($serialNumberOfMember == substr($serialNumberOfFile, 0, strlen($serialNumberOfMember)))
    {
        $templateRow = array();
        $templateRow['url'] = SecurityUtils::encodeUrl(ADMIDIO_URL.FOLDER_MODULES.'/documents-files.php', array('mode' => 'download', 'file_uuid' => $document['fil_uuid'], 'view' => 1));
        $templateRow['uuid'] = $document['fil_uuid'];
        $templateRow['name'] =  $document['fil_name'];
        
        $templateRow['actions'][] = array(
            'url' => SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php',
                array('mode' => 'download',
                    'file_uuid' => $document['fil_uuid'])),
            'icon' => 'bi bi-download',
            'tooltip' => $gL10n->get('SYS_DOWNLOAD_FILE')
        );
        
        $templateRow['actions'][] = array(
            'url' => SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php',
                array(
                    'mode' => 'file_rename',
                    'folder_uuid' => $pPreferences->config['settings']['folderUUID'],
                    'file_uuid' => $document['fil_uuid']
                )),
            'icon' => 'bi bi-pencil-square',
            'tooltip' => $gL10n->get('SYS_EDIT')
        );
        $templateRow['actions'][] = array(
            'url' => SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php',
                array('mode' => 'move',
                    'folder_uuid' => $pPreferences->config['settings']['folderUUID'],
                    'file_uuid' => $document['fil_uuid'])),
            'icon' => 'bi bi-folder-symlink',
            'tooltip' => $gL10n->get('SYS_MOVE_FILE')
        );
        
        $templateRow['actions'][] = array(
            'dataHref' => 'callUrlHideElement(\'row_' . $document['fil_uuid'] . '\', \'' . SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php',
                array('mode' => 'file_delete', 'file_uuid' => $document['fil_uuid'])) . '\', \'' . $gCurrentSession->getCsrfToken() . '\')',
            'dataMessage' => $gL10n->get('SYS_DELETE_ENTRY', array($document['fil_name'])),
            'icon' => 'bi bi-trash',
            'tooltip' => $gL10n->get('SYS_DELETE_FILE')
        );
        
        $documentsTemplateData[] = $templateRow;
    }
}

$page->assignSmartyVariable('documentsTemplateData', $documentsTemplateData);
$page->assignSmartyVariable('urlDocumentsFiles', SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php', array('folder_uuid' => $pPreferences->config['settings']['folderUUID'])));
$page->assignSmartyVariable('urlDocumentsPreferences', SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . '/Documents/system/preferences.php'));
$page->assignSmartyVariable('showDocumentsOnProfile', $gCurrentUser->isAdministratorDocumentsFiles());

	
