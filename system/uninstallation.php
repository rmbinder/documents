<?php
/**
 ***********************************************************************************************
 * Uninstallation of the Admidio plugin Documents
 *
 * @copyright rmb
 * @see https://github.com/rmbinder/documents/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 * Parameters:
 *
 * mode     : html   - show dialog for uninstallation
 *            uninst - uninstallation procedure
 *
 ***********************************************************************************************
 */

use Admidio\Infrastructure\Utils\FileSystemUtils;
use Admidio\Infrastructure\Utils\SecurityUtils;
use Admidio\Infrastructure\Exception;
use Admidio\Menu\Entity\MenuEntry;
use Admidio\Roles\Entity\Role;
use Admidio\Roles\Entity\RolesRights;
use Admidio\UI\Presenter\FormPresenter;
use Admidio\UI\Presenter\PagePresenter;
use Plugins\Documents\classes\Config\ConfigTable;

try
{
	require_once(__DIR__ . '/../../../system/common.php');
	require_once(__DIR__ . '/common_function.php');

	// only administrators are allowed to start this module
	if (!$gCurrentUser->isAdministrator())
	{
	    throw new Exception('SYS_NO_RIGHTS'); 
	}
    
	$pPreferences = new ConfigTable();
	$pPreferences->read();

	// Initialize and check the parameters
	$getMode                       = admFuncVariableIsValid($_GET, 'mode', 'string', array('defaultValue' => 'html', 'validValues' => array('html', 'uninst')));	
	$postUninstConfigData          = admFuncVariableIsValid($_POST, 'uninst_config_data', 'bool');
	$postUninstConfigDataOrgSelect = admFuncVariableIsValid($_POST, 'uninst_config_data_org_select', 'bool');
	
	switch ($getMode)
	{
		case 'html':
		
			global $gL10n;
			
			$title = $gL10n->get('PLG_DOCUMENTS_UNINSTALLATION');
			$headline =$gL10n->get('PLG_DOCUMENTS_UNINSTALLATION');
			
			$gNavigation->addUrl(CURRENT_URL, $headline);
			
			// create html page object
			$page = PagePresenter::withHtmlIDAndHeadline('plg-documents-uninstallation-html');
			$page->setTitle($title);
			$page->setHeadline($headline);
			
			$formUninstallation = new FormPresenter(
				'adm_preferences_form_uninstallation',
				'../templates/uninstallation.plugin.documents.tpl',
			    SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/uninstallation.php', array('mode' => 'uninst')),
				$page,
				array('class' => 'form-preferences')
			);
			
			$html = '<a class="btn btn-secondary" href="' . ADMIDIO_URL . FOLDER_PLUGINS . '/Documents/system/uninstallation.php">
            <i class="bi bi-trash"></i>' . $gL10n->get('PLG_DOCUMENTS_SWITCH_TO_UNINSTALLATION') . '</a>';
			$formUninstallation->addCustomContent(
			    'infofilechanges',
			    '',
			    '',
			    array('helpTextId' => 'PLG_DOCUMENTS_FILE_CHANGES_DESC', 'alertWarning' => $gL10n->get('PLG_DOCUMENTS_FILE_CHANGES_ALERT'))
			);
			
			$radioButtonEntries = array('0' => $gL10n->get('PLG_DOCUMENTS_ACTORGONLY'), '1' => $gL10n->get('PLG_DOCUMENTS_ALLORG') );
			
			$formUninstallation->addCheckbox('uninst_config_data', $gL10n->get('PLG_DOCUMENTS_REMOVE_CONFIG_DATA'));
			$formUninstallation->addRadioButton('uninst_config_data_org_select','',$radioButtonEntries, array('defaultValue' => '0'));
			
			$formUninstallation->addSubmitButton(
			    'adm_button_uninstallation',
			    $gL10n->get('PLG_DOCUMENTS_UNINSTALLATION'),
			    array('icon' => 'bi-trash', 'class' => 'offset-sm-3')
			);
			
			$formUninstallation->addToHtmlPage(false);
			
			$page->show();
			break;
		
		case 'uninst':
		    
		    $result = $gL10n->get('PLG_DOCUMENTS_UNINST_STARTMESSAGE');
		    
		    try
		    {
		        $templateFile = ADMIDIO_PATH . FOLDER_THEMES . '/simple/templates/modules/profile.view';           // ADMIDIO_PATH funktioniert ohne allow_url_open (PHP.ini)
		        $profileFile = ADMIDIO_PATH . FOLDER_MODULES . '/profile/profile';  
		        
		        FileSystemUtils::copyFile($templateFile.'_documents_save.tpl', $templateFile.'.tpl', array('overwrite' => true));
		        FileSystemUtils::deleteFileIfExists($templateFile.'_documents_save.tpl');
		        
		        FileSystemUtils::copyFile($profileFile.'_documents_save.php',$profileFile.'.php', array('overwrite' => true));
		        FileSystemUtils::deleteFileIfExists($profileFile.'_documents_save.php');
		        
		        $result .= $gL10n->get('PLG_DOCUMENTS_UNINST_FILE_CHANGES_SUCCESS');
		        
		        if ($postUninstConfigData)
		        {
		            $result_data = false;
		            $result_db = false;
		            
		            if (!$postUninstConfigDataOrgSelect)                    //Konfigurationsdaten nur in aktueller Org loeschen
		            {
		                $sql = 'DELETE FROM '.$pPreferences->config['Plugininformationen']['table_name'].'
        			              WHERE plp_name LIKE ?
        			                AND plp_org_id = ? ';
		                $result_data = $gDb->queryPrepared($sql, array($pPreferences->config['Plugininformationen']['shortcut'].'__%', $gCurrentOrgId));
		            }
		            else                                                    //Konfigurationsdaten in allen Org loeschen
		            {
		                $sql = 'DELETE FROM '.$pPreferences->config['Plugininformationen']['table_name'].'
        			              WHERE plp_name LIKE ? ';
		                $result_data = $gDb->queryPrepared($sql, array($pPreferences->config['Plugininformationen']['shortcut'].'__%'));
		            }
		            
		            // wenn die Tabelle nur Eintraege dieses Plugins hatte, sollte sie jetzt leer sein und kann geloescht werden
		            $sql = 'SELECT * FROM '.$pPreferences->config['Plugininformationen']['table_name'].' ';
		            $statement = $gDb->queryPrepared($sql);
		            
		            if ($statement->rowCount() == 0)
		            {
		                $sql = 'DROP TABLE '.$pPreferences->config['Plugininformationen']['table_name'].' ';
		                $result_db = $gDb->queryPrepared($sql);
		            }
		            
		            $result .= ($result_data ? $gL10n->get('PLG_DOCUMENTS_UNINST_DATA_DELETE_SUCCESS') : $gL10n->get('PLG_DOCUMENTS_UNINST_DATA_DELETE_ERROR') );
		            $result .= ($result_db ? $gL10n->get('PLG_DOCUMENTS_UNINST_TABLE_DELETE_SUCCESS') : $gL10n->get('PLG_DOCUMENTS_UNINST_TABLE_DELETE_ERROR') );
		        }
		    }
		    catch (\RuntimeException $exception)
		    {
		        $result .= $exception->getMessage();
		        // => EXIT
		    }
		    catch (\UnexpectedValueException $exception)
		    {
		        $result .= $exception->getMessage();
		        // => EXIT
		    }

		    $gNavigation->clear();
		    $gMessage->setForwardUrl($gHomepage);
		    
		    $gMessage->show($result);
			break;
	}

} catch (Exception $e) {
    $gMessage->show($e->getMessage());
}