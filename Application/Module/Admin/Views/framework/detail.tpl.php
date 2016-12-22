<div>
<?= $this->navigation ?>
</div>
<!-- [Zaciatok] Obsah clanku -->
<div class="article">
	<h2><?php echo $this->article->Title;?></h2>
		<span class="date">Publikované <span style="color: #777;"><?= $this->article->Registered ?></span> v rubrike
      <a href="/<?= $this->Privileges;?>/<?= $this->article->Category_unaccent;?>/default/"><?= $this->article->Category ?></a>
		</span>
	<div class="content"><?= $this->article->Content ?></div>
</div>
<!-- [Koniec] Obsah clanku -->

<!-- [Zaciatok] Bocne menu -->
<div id="menu">
	<h3>Menu</h3>
	<?= $this->menu ?>
</div>
<!-- [Koniec] Bocne menu -->
