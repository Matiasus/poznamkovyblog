<!-- [Zaciatok] Obsah clanku -->
<div class="article">
	<h2>Prehľad prihlásení</h2>
  <table id="table-home">
		<tr>
			<th><strong>Meno</strong></th>
			<th><strong>Datum</strong></th>
			<th><strong>IP</strong></th>
			<th><strong>Browser</strong></th>
			<th><strong>System</strong></th>
		</tr>
	<?php 
	foreach ($this->logins as $login){ ?>
		<tr>
			<td><?= $login->Username;?></td>
			<td><?= $login->Datum;?></td>
			<td><?= $login->Ip_address;?></td>
			<td><?= $login->Browser;?></td>
			<td><?= $login->System;?></td>
		</tr>
	<?php } ?>
	</table>
</div>
<!-- [Koniec] Obsah clanku -->
<!-- [Zaciatok] Bocne menu -->
<div id="menu">
	<h3>Menu</h3>
	<?= $this->menu ?>
</div>
<!-- [Koniec] Bocne menu -->
