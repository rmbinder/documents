<?php
/**
 ***********************************************************************************************
 * Configuration data for the Admidio plugin Documents
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 * 
 ***********************************************************************************************
 */

// The uuid of the folder in which the documents are located
$config_default['settings']['folderUUID'] = '';

// The field (profile field) with the member's serial number    
// The following options are available:
//      - usr_id                                
//      - usr_uuid                             
//      - the internal name of a profile field 
$config_default['settings']['serialNumberField'] = ''; 

// Pad the serial number with zeros   
// As a member number (or serial number) Admidio uses e.g. '1' or '12' or '111'
// However, for the file names of the documents, use (for better sorting) 0001 or 0012 or 0111
// The number set here indicates how many digits the serial number is extended to by leading zeros
// Example: The file name is ''0015-Mustermann Max-declaration of membership.pdf' and the membership number in Admidio is 15
//  --> With '$config_default['settings']['maxPositions'] = 4;' the member number becomes '0015' and the comparison is positive.
// If the serial number already has more characters than defined in $config_default['settings']['maxPositions'], the serial number is not changed (i.e. not shortened)
// If $config_default['settings']['maxPositions'] = 0, NO leading zeros are added, the serial number is used as is
$config_default['settings']['maxPositions'] = 0; 

// Separator
// If you work with file names without leading zeros in the serial number
// e.g. '12-Meier Albert-declaration of membership.pdf' or '2-Meier Hilde-declaration of membership.pdf',
// so you can use the separator to split the file name into a serial number and a remainder
// The first occurrence of the separator is searched for and the file name is separated at this point
// Example of a file name with the separator '-': '2-Meier Hilde-declaration of membership.pdf'
// In this example, the file name is split into '2' and 'Meier Hilde-declaration of membership.pdf'
// The number '2' is used for further comparison with the member number (or user_id...).
$config_default['settings']['separator'] = ''; 
      
$config_default['Plugininformationen']['version'] = '';
$config_default['Plugininformationen']['stand'] = '';

/*
 *  Mittels dieser Zeichenkombination werden Konfigurationsdaten, die zur Laufzeit als Array verwaltet werden,
 *  zu einem String zusammengefasst und in der Admidiodatenbank gespeichert.
 *  Muessen die vorgegebenen Zeichenkombinationen (#_#) jedoch ebenfalls, z.B. in der Beschreibung
 *  einer Konfiguration, verwendet werden, so kann das Plugin gespeicherte Konfigurationsdaten
 *  nicht mehr richtig einlesen. In diesem Fall ist die vorgegebene Zeichenkombination abzuaendern (z.B. in !-!)
 *
 *  Achtung: Vor einer Aenderung muss eine Deinstallation durchgefuehrt werden!
 *  Bereits gespeicherte Werte in der Datenbank koennen nach einer Aenderung nicht mehr eingelesen werden!
 */
$dbtoken  = '#_#';  
