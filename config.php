<?php
/**
 ***********************************************************************************************
 * Configuration file for Admidio plugin documents
 *
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 * 
 **********************************************************************************************
 * 
 * 'documents' allows you to display user-related documents in a member's profile.
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
 ***********************************************************************************************
 */

// The uuid of the folder in which the documents are located
// Note: The folder_uuid is displayed when you move the mouse over the respective folder in 'Documents & Files' (not click)
// --> e.g. http://localhost/adm_program/modules/documents-files/documents_files.php?folder_uuid=9a4c4c66-f91b-46e9-9a98-d3554545b822
// ATTENTION: The specified folder_uuid is only an example, it must be replaced in any case.
$plg_documents_folderUUID = '9a4c4c66-f91b-46e9-9a98-d3554545b822';

// The field (profile field) with the member's serial number
// The following options are available:
//      - usr_id                                --> $plg_documents_serialNumberField = 'usr_id';
//      - usr_uuid                              --> $plg_documents_serialNumberField = 'usr_uuid';
//      - the internal name of a profile field  --> $plg_documents_serialNumberField = 'MEMBERNUMBER1';
// ATTENTION: The specified internal name is for example only
$plg_documents_serialNumberField = 'usr_id';

// Pad the serial number with zeros
// As a member number (or serial number) Admidio uses e.g. '1' or '12' or '111'
// However, for the file names of the documents, use (for better sorting) 0001 or 0012 or 0111
// The number set here indicates how many digits the serial number is extended to by leading zeros
// Example: The file name is ''0015-Mustermann Max-declaration of membership.pdf' and the membership number in Admidio is 15
//  --> With '$plg_documents_maxPositions = 4;' the member number becomes '0015' and the comparison is positive.
// If the serial number already has more characters than defined in $plg_documents_maxPositions, the serial number is not changed (i.e. not shortened)
// If $plg_documents_maxPositions = 0, NO leading zeros are added, the serial number is used as is
$plg_documents_maxPositions = 0;

// Separator
// If you work with file names without leading zeros in the serial number
// e.g. '12-Meier Albert-declaration of membership.pdf' or '2-Meier Hilde-declaration of membership.pdf',
// so you can use the separator to split the file name into a serial number and a remainder
// The first occurrence of the separator is searched for and the file name is separated at this point
// Example of a file name with the separator '-': '2-Meier Hilde-declaration of membership.pdf'
// In this example, the file name is split into '2' and 'Meier Hilde-declaration of membership.pdf'
// The number '2' is used for further comparison with the member number (or user_id...).
// Example: $plg_documents_separator = '-';
$plg_documents_separator = '-';
