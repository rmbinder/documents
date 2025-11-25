<!-- Documents Accordion -->
{if $showDocumentsOnProfile}
    <div class="accordion-item">
        <h2 class="accordion-header" id="adm_profile_documents_accordion_heading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adm_profile_documents_accordion" aria-expanded="false" aria-controls="adm_profile_documents_accordion">
                {$l10n->get('PLG_DOCUMENTS_NAME')}
            </button>
        </h2>
        <div id="adm_profile_documents_accordion" class="accordion-collapse collapse" aria-labelledby="adm_profile_documnets_accordion_heading" data-bs-parent="#adm_profile_accordion">
            <div class="accordion-body">
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
        </div>
    </div>
{/if}
