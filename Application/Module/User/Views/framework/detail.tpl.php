<!-- [Zaciatok] Obsah clanku -->
<div class="article">
	<h2><?php echo $this->article->Title;?></h2>
		<span class="date">Publikované <span style="color: #777;"><?= $this->article->Registered?></span> v rubrike
      <a href="/<?= $this->Privileges;?>/<?= $this->article->category_url;?>/default/"><?= $this->article->Category;?></a>
		</span>
	<div class="content"><?php print $this->article->Content;?></div>
</div>
<!-- [Koniec] Obsah clanku -->

<!-- [Zaciatok] Bocne menu -->
<div id="menu">
	<h3>Menu</h3>
	<?php echo $this->menu; ?>
</div>
<!-- [Koniec] Bocne menu -->
