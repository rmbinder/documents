<form {foreach $attributes as $attribute}
        {$attribute@key}="{$attribute}"
    {/foreach}>

    {include 'sys-template-parts/form.select.tpl' data=$elements['documents_folder_uuid']}
    {include 'sys-template-parts/form.select.tpl' data=$elements['documents_serialNumberField']}
    {include 'sys-template-parts/form.input.tpl' data=$elements['documents_maxPositions']}
    {include 'sys-template-parts/form.input.tpl' data=$elements['documents_separator']}            
                 
    {include 'sys-template-parts/form.button.tpl' data=$elements['adm_button_save_options']} 
    <div class="form-alert" style="display: none;">&nbsp;</div>
</form>
