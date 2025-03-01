<?php
/**
 ***********************************************************************************************
 * documents
 *
 * This plugin lists member related documents in the member profile.
 * 
 * Version 1.0
 * 
 * Version date: 01.03.2025
 * 
 * Author: rmb
 * 
 * Compatible with Admidio version 4.3
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

require_once(__DIR__ . '/../../adm_program/system/common.php');
require_once(__DIR__ . '/config.php'); 

if (!$gCurrentUser->adminDocumentsFiles())
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
$documentsFolder = new TableFolder($gDb);
$documentsFolder->readDataByUuid($plg_documents_folderUUID);

// read all files in the documents folder
$documents = $documentsFolder->getFilesWithProperties();

$page->addHtml('<div class="card admidio-field-group" id="documents_box">
				<div class="card-header">'.$gL10n->get('PLG_DOCUMENTS_HEADLINE'));

$page->addHtml('<a class="btn btn-secondary float-right" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files/documents_files.php', array(
                    'folder_uuid' => $plg_documents_folderUUID)). '">
                    <i class="fas fa-edit" ></i>' . $gL10n->get('SYS_EDIT') . '
    	        </a>');

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
        
        $page->addHtml('<a href="'.SecurityUtils::encodeUrl(ADMIDIO_URL.FOLDER_MODULES.'/documents-files/get_file.php', array('file_uuid' => $document['fil_uuid'], 'view' => 1)).'">'.$document['fil_name'].'</a>');
        
        $page->addHtml('</div><div style="text-align: right;float:right;">');
        
        // Icon link to rename the file
        $page->addHtml('<a class="admidio-icon-link" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files/rename.php', array('folder_uuid' => $plg_documents_folderUUID, 'file_uuid' => $document['fil_uuid'])). '">
                              <i class="fas fa-edit" data-toggle="tooltip" title="'.$gL10n->get('SYS_EDIT').'"></i>
                       </a>');
            
        // Icon link to move the file
        $page->addHtml('<a class="admidio-icon-link" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_MODULES . '/documents-files/move.php', array('folder_uuid' => $plg_documents_folderUUID, 'file_uuid' => $document['fil_uuid'])). '">
                              <i class="fas fa-folder" data-toggle="tooltip" title="'.$gL10n->get('SYS_MOVE_FILE').'"></i>
                       </a>');
        
        // The icon link to delete the file works, but the page is not refreshed. The file is still displayed after deletion --> unusable
       /*     $page->addHtml('<a class="admidio-icon-link openPopup" href="javascript:void(0);"
                data-href="'.SecurityUtils::encodeUrl(ADMIDIO_URL.'/adm_program/system/popup_message.php', 
                    array('type' => 'fil', 'element_id' => $document['fil_uuid'], 'name' => $document['fil_name'], 'database_id' => $document['fil_uuid'], 'database_id_2' => $plg_documentdisplay_folderUUID)). '">
                <i class="fas fa-trash-alt" data-toggle="tooltip" title="'.$gL10n->get('SYS_DELETE_FILE').'"></i></a>');*/
         
        // File deletion is therefore done via own script
        $page->addHtml('<a class="admidio-icon-link" href="'. SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/file_delete.php', array('mode' => 1, 'file_uuid' => $document['fil_uuid'], 'name' => $document['fil_name'] )). '">
                              <i class="fas fa-trash-alt" data-toggle="tooltip" title="'.$gL10n->get('SYS_DELETE_FILE').'"></i>
                       </a>');
                    
        $page->addHtml('</div>');//Float right
        $page->addHtml('<div style="clear:both"></div></li>');
    }
}

$page->addHtml('</ul></div></div>');
//Move content to correct position by jquery
$page->addHtml('<script>$("#documents_box").insertBefore("#profile_roles_box");</script>');

	
