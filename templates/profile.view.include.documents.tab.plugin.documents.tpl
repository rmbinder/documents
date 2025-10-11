<!-- Documents Tab -->
{if $showDocumentsOnProfile}
    <div class="tab-pane fade" id="adm_profile_documents_pane" role="tabpanel" aria-labelledby="adm_profile_documents_tab">
        <a class="admidio-icon-link float-end" href="{$urlDocumentsPreferences}">
            <i class="bi bi-gear-fill" title="{$l10n->get('SYS_SETTINGS')}"></i>
        </a>                      
        <a class="admidio-icon-link float-end" href="{$urlDocumentsFiles}">
            <i class="bi bi-pencil-square" title="{$l10n->get('PLG_DOCUMENTS_EDIT_DOCUMENTS')}"></i>
        </a>  
        <table id="adm_documents_table" class="table table-hover" width="100%" style="width: 100%;">
            <tbody>
                {foreach $documentsTemplateData as $row}
                    <tr id="row_{$row.uuid}">
                        <td style="word-break: break-word;"><a href="{$row.url}">{$row.name}</a></td>
                        <td class="text-end">
                            {include 'sys-template-parts/list.functions.tpl' data=$row}    
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{/if}
