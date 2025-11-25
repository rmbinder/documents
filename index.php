<?php
/**
 ***********************************************************************************************
 * Documents / Dokumente
 *
 * Version 2.0
 *
 * This plugin lists member related documents in a member's profile.
 * 
 * Several requirements must be met for this to happen:
 * 
 * 1. All documents must be located in a 'Documents & Files' folder
 * 
 * 2. All documents must be preceded by a sequential number
 *      e.g. '0015-Mustermann Max-declaration of membership.pdf'
 *      or '65-Meier Franz-termination.jpg'
 * 
 * 3. The sequential number can either be 
 *      - the member's 'usr_id'
 *      - the member's 'usr_uuid'
 *      - or a profile field with a sequential number (e.g. member number)
 *        (Note: The Membership Fee plugin can create corresponding membership numbers)
 *        
 * The plugin checks whether the sequential number of a document matches the member number
 *  (usr_id, usr_uuid or member numer) and then displays the document.
 *
 * Author: rmb
 *
 * Compatible with Admidio version 5
 * 
 * Usage:
 * 
 * To install, run the file .../system/install.php.
 * 
 * All further settings are made in the built-in Preferences routine.
 *
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

//Fehlermeldungen anzeigen
//error_reporting(E_ALL);

try {
    require_once(__DIR__ . '/../../system/common.php');
    require_once(__DIR__ . '/system/common_function.php');

    $urlInst =  ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER. '/system/install.php';
    
    $gMessage->show($gL10n->get('PLG_DOCUMENTS_CALLING_DIRECTLY_INFO', array('<a href="' . $urlInst .'">' . $urlInst . '</a>')));
                          
} catch (Exception $e) {
    $gMessage->show($e->getMessage());
}
