{literal}
<script language="JavaScript1.2" type="text/javascript">
<!--
function toggleCheckboxes( formname, checkboxname )
{
    with( formname )
        {
        for( var i=0; i<elements.length; i++ )
        {
            if( elements[i].type == 'checkbox' && elements[i].name == checkboxname && elements[i].disabled == "" )
            {
                if( elements[i].checked == true )
                {
                    elements[i].checked = false;
                }
                else
                {
                    elements[i].checked = true;
                }
            }
            }
    }
}
//-->
</script>
{/literal}

<div class="context-block">
<h2 class="context-title">{'RSS exports [%exports_count]'|i18n( 'design/admin/rss/list',, hash( '%exports_count', $rssexport_list|count ) )}</h2>

<form name="rssexportslist" action={'rss/list'|ezurl} method="post" name="RSSExport">

<table class="list" cellspacing="0">
{section show=$rssexport_list|count}
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="Invert selection." onclick="toggleCheckboxes( document.rssexportslist, 'DeleteIDArray[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/content/trash' )}" /></th>
    <th>{'Name'|i18n( 'design/admin/rss/list' )}</th>
    <th>{'Version'|i18n( 'design/admin/rss/list' )}</th>
    <th>{'Status'|i18n( 'design/admin/rss/list' )}</th>
    <th>{'Modifier'|i18n( 'design/admin/rss/list' )}</th>
    <th>{'Modified'|i18n( 'design/admin/rss/list' )}</th>
    <th class="tight">&nbsp;</th>
</tr>

{section var=RSSExports loop=$rssexport_list sequence=array( bglight, bgdark )}
<tr>
    <td><input type="checkbox" name="DeleteIDArray[]" value="{$RSSExports.item.id}"></td>
    <td><a href={concat("rss/feed/",$RSSExports.item.access_url)|ezurl}>{$RSSExports.item.title|wash}</a></td>
    <td>{$RSSExports.item.rss_version|wash}</td>
    <td>{section show=$RSSExports.item.active|eq(1)}{"Active"|i18n( 'design/admin/rss/list' )}{section-else}{"Inactive"|i18n( 'design/admin/rss/list' )}{/section}</td>
    <td>{content_view_gui view=text_linked content_object=$RSSExports.item.modifier.contentobject}</td>
    <td>{$RSSExports.item.modified|l10n(shortdatetime)}</td>
    <td><a href={concat("rss/edit_export/",$RSSExports.item.id)|ezurl}><img class="button" src={"edit.png"|ezimage} width="16" height="16" alt="Edit" /></a></td>
</tr>
{/section}
{section-else}
<tr class="bglight"><td>{'The RSS export list is empty.'|i18n( 'design/admin/rss/list' )}</td></tr>
{/section}
</table>

<div class="controlbar">
<div class="block">
{section show=$rssexport_list|count}
<input class="button" type="submit" name="RemoveExportButton" value="{'Remove selected'|i18n( 'design/admin/rss/list' )}" />
{section-else}
<input class="button" type="submit" name="RemoveExportButton" value="{'Remove selected'|i18n( 'design/admin/rss/list' )}" disabled="disabled" />
{/section}

<input class="button" type="submit" name="NewExportButton" value="{'New export'|i18n( 'design/admin/rss/list' )}" />
</div>
</div>

</div>

</form>


<form name="rssimportslist" action={'rss/list'|ezurl} method="post" name="RSSImport">

<div class="context-block">
<h2 class="context-title">{'RSS imports [%imports_count]'|i18n( 'design/admin/rss/list',, hash( '%imports_count', $rssimport_list|count ) )}</h2>

<table class="list" cellspacing="0">
{section show=$rssimport_list|count}
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="Invert selection." onclick="toggleCheckboxes( document.rssimportslist, 'DeleteIDArrayImport[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/content/trash' )}" /></th>
    <th>{"Name"|i18n( 'design/admin/rss/list' )}</th>
    <th>{"Status"|i18n( 'design/admin/rss/list' )}</th>
    <th>{"Modifier"|i18n( 'design/admin/rss/list' )}</th>
    <th>{"Modified"|i18n( 'design/admin/rss/list' )}</th>
    <th class="tight">&nbsp;</th>
</tr>

{section var=RSSImports loop=$rssimport_list sequence=array(bglight,bgdark)}
<tr>
    <td><input type="checkbox" name="DeleteIDArrayImport[]" value="{$RSSImports.item.id}"></td>
    <td><a href={concat("rss/edit_import/",$RSSImports.item.id)|ezurl}>{$RSSImports.item.name|wash}</a></td>
    <td>{section show=$RSSImports.item.active|eq(1)}{"Active"|i18n( 'design/admin/rss/list' )}{section-else}{"Inactive"|i18n( 'design/admin/rss/list' )}{/section}</td>
    <td>{content_view_gui view=text_linked content_object=$RSSImports.item.modifier.contentobject}</td>
    <td><span class="small">{$RSSImports.item.modified|l10n(shortdatetime)}</span></td>
    <td><a href={concat("rss/edit_import/",$RSSImports.item.id)|ezurl}><img class="button" src={"edit.png"|ezimage} width="16" height="16" alt="Edit" /></a></td>
</tr>
{/section}
{section-else}
<tr class="bglight"><td>{'The RSS import list is empty.'|i18n( 'design/admin/rss/list' )}</td></tr>
{/section}
</table>

<div class="controlbar">
<div class="block">
{section show=$rssimport_list|count}
<input class="button" type="submit" name="RemoveImportButton" value="{'Remove selected'|i18n( 'design/admin/rss/list' )}" />
{section-else}
<input class="button" type="submit" name="RemoveImportButton" value="{'Remove selected'|i18n( 'design/admin/rss/list' )}" disabled="disabled" />
{/section}

<input class="button" type="submit" name="NewImportButton" value="{'New import'|i18n( 'design/admin/rss/list' )}" />
</div>
</div>

</div>

</form>
