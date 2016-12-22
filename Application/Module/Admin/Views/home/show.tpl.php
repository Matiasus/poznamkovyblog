	<table id="table-home">
		<tr>
			<th width="280px"><strong>Názov článku</strong></th>
			<th width="100px"><strong>Rubrika</strong></th>
			<th width="90px"><strong>Author</strong></th>
			<th width="140px"><strong>Dátum vloženia</strong></th>
			<th><strong>Zmena</strong></th>
		</tr>

	<?php foreach ($this->authorarticles as $article){ ?>
		<tr>
			<td>
			 <a href="/<?php echo $this->user->Privileges, '/', $article->posted, '/show/', $article->Urldescript;?>/">
				<?php echo $article->Title;?>
			 </a>
			</td>
			<td>
			 <a href="/<?php echo $this->user->Privileges, '/', $article->posted, '/default';?>/">
				<?php echo $article->Posted;?>
			 </a>
			</td>
			<td>
			 <a href="/<?php echo $this->user->Privileges, '/home/show/', $article->author;?>/">
				<?php echo $article->Author?>
			 </a>
			</td>
			<td>
				<?php echo $article->Published?>
			</td>
			<td>
			 <a href="/<?php echo $this->user->Privileges, '/form/editujclanok/', $article->Urldescript;?>/">Upraviť</a>

		</tr>
	<?php } ?>
	</table>

