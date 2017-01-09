<div class="article">
	<h2>Zoznam článkov</h2>
	<table id="table-home">
		<tr>
			<th width="280px"><strong>Názov článku</strong></th>
			<th width="150px"><strong>Rubrika</strong></th>
			<th width="90px"><strong>Autor</strong></th>
			<th width="140px"><strong>Dátum vloženia</strong></th>
		<?php if (strcmp($this->Privileges, "admin") === 0 ) {?>
			<th width="100px"><strong>Zmena</strong></th>
		<?php }?>
		</tr>
	<?php 
	foreach ($this->articles as $article){ ?>
		<tr>
			<td>
			 <a href="/<?php echo $this->Privileges, '/', $article->category, '/show/', $article->Title_Url, '/', $article->registered;?>/">
				<?php echo $article->Title;?>
			 </a>
			</td>
			<td>
			 <a href="/<?php echo $this->Privileges, '/', $article->category, '/default';?>/">
				<?php echo $article->Category;?>
			 </a>
			</td>
			<td>
			 <a href="/<?php echo $this->Privileges, '/home/show/', $article->username;?>/">
				<?php echo $article->Username;?>
			 </a>
			</td>
			<td>
				<?php echo $article->Registered;?>
			</td>
			<td>
				<?php if (strcmp($this->Privileges, "admin") === 0 ) {?>
			 <a href="/<?php echo $this->Privileges, '/articles/edit/', $article->Url, '/', $article->registered;?>/">Upraviť</a>
				<?php }?>
			</td>

		</tr>
	<?php } ?>
	</table>
</div>
<!-- [Zaciatok] Bocne menu -->
<div id="menu">
	<h3>Menu</h3>
	<?php echo $this->menu; ?>
</div>
<!-- [Koniec] Bocne menu -->
