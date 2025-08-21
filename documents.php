<?php
/**
 ***********************************************************************************************
 * documents
 *
 * This plugin lists member related documents in the member profile.
 * 
 * Version 2.0 Beta 1
 * 
 * Version date: 01.03.2025
 * 
 * Author: rmb
 * 
 * Compatible with Admidio version 5.0
 *
 *
 * Usage:
 * 
 * Add the following line to adm_program/modules/profile/profile.php ( before $page->show(); ):
 * require_once(ADMIDIO_PATH . FOLDER_PLUGINS .'/documents/documents.php');
 * 
 * All further settings are made in the config.php
 *
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 ***********************************************************************************************
 */

use Admidio\Documents\Entity\Folder;
use Admidio\Infrastructure\Utils\SecurityUtils;

require_once(__DIR__ . '/../../system/common.php');
require_once(__DIR__ . '/config.php'); 

if (!$gCurrentUser->isAdministratorDocumentsFiles())
{
    return;
    // => EXIT
}

if(!defined('PLUGIN_FOLDER'))
{
    define('PLUGIN_FOLDER', '/'.substr(__DIR__,strrpos(__DIR__,DIRECTORY_SEPARATOR)+1));
}

$serialNumberOfMember = $user->getValue($plg_documents_serialNumberField);

if ($plg_documents_maxPositions !== 0 && strlen($serialNumberOfMember) < $plg_documents_maxPositions)
{
    $serialNumberOfMember = str_pad($serialNumberOfMember, $plg_documents_maxPositions, '0', STR_PAD_LEFT );
}

// get recordset of documents folder from database
$documentsFolder = new Folder($gDb);
$documentsFolder->readDataByUuid($plg_documents_folderUUID);

// read all files in the documents folder
$documents = $documentsFolder->getFilesWithProperties();

$page->addHtml('<div class="card admidio-field-group" id="documents_box">
				<div class="card-header">'.$gL10n->get('PLG_DOCUMENTS_HEADLINE'));

$page->addHtml('<div style="text-align: right;float:right;">');
$page->addHtml('<a class="btn btn-secondary float-right" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php', array(
                    'folder_uuid' => $plg_documents_folderUUID)). '">
                    <i class="bi bi-pencil-square" ></i>' . $gL10n->get('SYS_EDIT') . '
    	        </a>');
$page->addHtml('</div>');//Float right

$page->addHtml('</div><div id="documents_box_body" class="card-body">');

foreach ($documents as $document)
{
    If ($plg_documents_separator !== '')
    {         
        $serialNumberOfFile = substr($document['fil_name'], 0, strpos($document['fil_name'], $plg_documents_separator));
    }
    else 
    {
        $serialNumberOfFile =  $document['fil_name'];
    }
    
    if ($serialNumberOfMember == substr($serialNumberOfFile, 0, strlen($serialNumberOfMember)))
    {
        $page->addHtml('<li class= "list-group-item">');
        $page->addHtml('<div style="text-align: left;float:left;">');
        
        $page->addHtml('<a href="'.SecurityUtils::encodeUrl(ADMIDIO_URL.FOLDER_MODULES.'/documents-files.php', array('mode' => 'download', 'file_uuid' => $document['fil_uuid'], 'view' => 1)).'">'.$document['fil_name'].'</a>');
        
        $page->addHtml('</div><div style="text-align: right;float:right;">');
        
        // Icon link to show the file
        $page->addHtml('<a class="admidio-icon-link" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php', array('mode' => 'download', 'file_uuid' => $document['fil_uuid'])). '">
                              <i class="bi bi-download" data-toggle="tooltip" title="'.$gL10n->get('SYS_DOWNLOAD_FILE').'"></i>
                       </a>');
        
        // Icon link to rename/edit the file
        $page->addHtml('<a class="admidio-icon-link" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php', array('mode' => 'file_rename','folder_uuid' => $plg_documents_folderUUID, 'file_uuid' => $document['fil_uuid'])). '">
                              <i class="bi bi-pencil-square" data-toggle="tooltip" title="'.$gL10n->get('SYS_EDIT').'"></i>
                       </a>');
            
        // Icon link to move the file
        $page->addHtml('<a class="admidio-icon-link" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php', array('mode' => 'move','folder_uuid' => $plg_documents_folderUUID, 'file_uuid' => $document['fil_uuid'])). '">
                              <i class="bi bi-folder-symlink" data-toggle="tooltip" title="'.$gL10n->get('SYS_MOVE_FILE').'"></i>
                       </a>');
        
        
        
        // Icon link to delete the file
        // The icon link to delete the file works, but the page is not refreshed. The file is still displayed after deletion --> unusable (but will be used until a better solution is found)
        $page->addHtml('
                    <a class="admidio-icon-link admidio-messagebox" href="javascript:void(0);" data-buttons="yes-no"
                         data-message="' . $gL10n->get('SYS_DELETE_ENTRY', array($document['fil_name'])) . '"
                        data-href="callUrlHideElement(\'no_element\', \'' . SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files.php', 
                            array('mode' => 'file_delete', 'file_uuid' => $document['fil_uuid'])) . '\', \'' . $gCurrentSession->getCsrfToken() . '\')">
                        <i class="bi bi-trash" data-bs-toggle="tooltip" title="' . $gL10n->get('SYS_DELETE_FILE') . '"></i></a>');
        
        $page->addHtml('</div>');//Float right
        $page->addHtml('<div style="clear:both"></div></li>');
    }
}

$page->addHtml('</ul></div></div>');
//Move content to correct position by jquery
$page->addHtml('<script>$("#documents_box").insertBefore("#profile_roles_box");</script>');

	
