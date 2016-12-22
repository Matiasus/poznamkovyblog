<div>
<?= $this->navigation ?>
</div>
<!-- [Zaciatok] Obsah clanku -->
<div class="article">
	<h2>Výpočet hysterézie</h2>
		<span class="date">Publikované <span style="color: #777;">2016-11-20 13:21:00</span> v rubrike
      <a href="/<?= $this->Privileges;?>/tools/default/"><?= $this->article->Category;?></a>
		</span>
	<div class="content">
    <?= $this->article->Content;?>
  </div>
</div>
<!-- [Koniec] Obsah clanku -->

<!-- [Zaciatok] Bocne menu -->
<div id="menu">
	<h3>Menu</h3>
	<?= $this->menu ?>
</div>
<!-- [Koniec] Bocne menu -->
