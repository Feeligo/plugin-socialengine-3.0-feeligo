{if $flg_is_enabled == true}
{literal}
<link rel="stylesheet" href="{/literal}{$flg_css_url}{literal}" title="stylesheet" type="text/css" />
<!--[if lte IE 7]>
<link rel="stylesheet" href="{/literal}{$flg_css_ie7_url}{literal}" title="stylesheet" type="text/css" />	
<![endif]-->
{/literal} 
{literal}
<script type="text/javascript">
  {/literal}{$flg_initialization_js}{literal}
</script>
{/literal}
  
{/if}