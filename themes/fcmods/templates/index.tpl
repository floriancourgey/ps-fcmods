{extends file='page.tpl'}

{block name='page_content_container'}
  bienvenue sur {$category->name}
 
  Seances :

  {foreach from=$seances item=seance}
    <div id="{$seance.id}" style="height:100px;">
      <a href="#{$seance.id}">Seance de {$seance.type} de {$seance.de} a {$seance.a}</a>
    </div>
  {/foreach}
{/block}
