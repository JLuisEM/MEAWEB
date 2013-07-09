	<script src="{$this_path}js/jquery.kwicks-1.5.1.js" type="text/javascript"></script>
    	<script type="text/javascript">
        {literal}
			$().ready(function() {
				$('.kwicks').kwicks({
					max : {/literal}{$width}{literal} ,
					spacing : 0,
                    duration: {/literal}{$changeSpeed}{literal},    
				});
			});
        {/literal}
		</script>
    {assign var="outerHeight" value=$height}
    {if $hookslider == 'top'}
        </div>
    {/if}
    {foreach from=$destaques item=destaque name=destaques}
            {if $destaque.logo}
            	{assign var=total value=$smarty.foreach.destaques.index}
            {/if}
    {/foreach}
<div id="accordion_slider" style="width:{$width}px;height:{$outerHeight}px">
	<ul class="kwicks">
    {foreach from=$destaques item=destaque name=destaques}
            {if $destaque.logo}
            	{assign var=current value=$smarty.foreach.destaques.index}
        
                 <li id="kwick_{$current}" class="perslider" style="height:{$height}px;width:{$width/($total+1)}px;">
				  <a href="{$destaque.logo_link}">
                        <span class="feature_excerpt" style="width:{$width-30}px;">
                            <span class="position_excerpt" style="display: block; position: absolute; ">
						      {$destaque.logo_title}
						</span>
                    </span>
                    <span class="fadeout" style="height:{$height}px;"></span>
						<img src="{$this_path}slider_{$current}.jpg" alt="{$destaque.logo_title}" />
				  </a>
                </li> 
            
            {/if}
        

    {/foreach}
    </ul>
</div>
    
    {if $hookslider == 'top'}
        <div>
    {/if}
    
    {if $hookslider == 'home'}
        <div class="clear"></div>
    {/if}
