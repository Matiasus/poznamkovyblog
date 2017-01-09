//<![CDATA[

	/***
	 * Kalendar 
	 */
	function Calendar(id)
  {

		/*
		** Pole obsahujuce meniny na prislusny datum v roku
		*/			
		var meniny = new Array(

			/* Januar */
			new Array("Nový rok", "Alexandra, Karina", "Daniela", "Drahoslav", "Andrea", "Antónia", "Bohuslava", "Severín", "Alexej", "Dáša", "Malvína", "Ernest", "Rastislav", "Radovan", "Dobroslav", "Kristína", "Nataša", "Bohdana", "Drahomíra, Sára, Mário", "Dalibor", "Vincent", "Zora", "Miloš", "Timotej", "Gejza", "Tamara", "Bohuš", "Alfonz", "Gašpar", "Ema", "Emil"),

			/* Februar */
			new Array("Tatiana", "Erik, Erika", "Blažej", "Veronika", "Agáta", "Dorota", "Vanda", "Zoja", "Zdenko", "Gabriela", "Dezider", "Perla", "Arpád", "Valentín", "Pravoslav", "Ida, Liana", "Miloslava", "Jaromír", "Vlasta", "Lívia", "Eleonóra", "Etela", "Roman, Romana", "Matej", "Frederik, Federika", "Viktor", "Alexander", "Zlatica", "Radomír"),

			/* Marec */
			new Array("Albín", "Anežka", "Bohumil, Bohumila", "Kazimír", "Fridrich", "Radoslav, Radoslava", "Tomáš", "Alan, Alana", "Františka", "Branislav, Bruno", "Angela, Angelika", "Gregor", "Vlastimil", "Matilda", "Svetlana", "Boleslav", "Ľubica", "Eduard", "Jozef", "Víťazoslav", "Blahoslav", "Beňadik", "Adrián", "Gabriel", "Marián", "Emanuel", "Alena", "Soňa", "Miroslav", "Vieroslava", "Benjamín"),

			/* April */
			new Array("Hugo", "Zita", "Richard", "Izidor", "Miroslava", "Irena", "Zoltán", "Albert", "Milena", "Igor", "Július", "Estera", "Aleš", "Justína", "Fedor", "Dana, Danica", "Rudolf", "Valér", "Jela", "Marcel", "Ervín", "Slavomír", "Vojtech", "Juraj", "Marek", "Jaroslava", "Jaroslav", "Jarmila", "Lea", "Anastázia"),

			/* May */
			new Array("Sviatok práce", "Žigmund", "Galina", "Florián", "Lesana, Lesia", "Hermína", "Monika", "Ingrida", "Roland", "Viktória", "Blažena", "Pankrác", "Servác", "Bonifác", "Žofia", "Svetozár", "Gizela", "Viola", "Gertrúda", "Bernard", "Zina", "Júlia, Juliana", "Želmíra", "Ela", "Urban", "Dušan", "Iveta", "Viliam", "Vilma", "Ferdinand", "Petronela, Petrana"),

			/* Jun */
			new Array("Žaneta", "Xénia, Oxana", "Karolína", "Lenka", "Laura", "Norbert", "Róbert", "Medard", "Stanislava", "Margaréta", "Dobroslava", "Zlatko", "Anton", "Vasil", "Vít", "Blanka", "Adolf", "Vratislav, Vratislava", "Alfréd", "Valéria", "Alojz", "Paulína", "Sidónia", "Ján", "Tadeáš", "Adriána", "Ladislav, Ladislava", "Beáta", "Peter a Pavol, Petra", "Melánia"),

			/* Jul */
			new Array("Diana", "Berta", "Miloslav", "Prokop", "Cyril, Metod", "Patrícia, Patrik", "Oliver", "Ivan", "Lujza", "Amália", "Milota", "Nina", "Margita", "Kamil", "Henrich", "Drahomír", "Bohuslav", "Kamila", "Dušana", "Iľja, Eliáš", "Daniel", "Magdaléna", "Oľga", "Vladimír", "Jakub", "Anna, Hana", "Božena", "Krištof", "Marta", "Libuša", "Ignác"),

			/* August */
			new Array("Božidara", "Gustáv", "Jerguš", "Dominik, Dominika", "Hortenzia", "Jozefína", "Štefánia", "Oskár", "Ľubomíra", "Vavrinec", "Zuzana", "Darina", "Ľubomír", "Mojmír", "Marcela", "Leonard", "Milica", "Elena, Helena", "Lýdia", "Anabela", "Jana", "Tichomír", "Filip", "Bartolomej", "Ľudovít", "Samuel", "Silvia", "Augustín", "Nikola", "Ružena", "Nora"),

			/* September */
			new Array("Drahoslava", "Linda", "Belo", "Rozália", "Regína", "Alica", "Marianna", "Miriama", "Martina", "Oleg", "Bystrík", "Mária", "Ctibor", "Ľubomil, Ľudomil", "Jolana", "Ľudmila", "Olympia", "Eugénia", "Konštantín", "Ľuboslav, Ľuboslava", "Matúš", "Móric", "Zdenka", "Ľuboš, Ľubor", "Vladislav", "Edita", "Cyprián", "Václav", "Michal, Michaela", "Jarolím"),

			/* Oktober */
			new Array("Arnold", "Levoslav", "Stela", "František", "Viera", "Natália", "Eliška", "Brigita", "Dionýz", "Slavomíra", "Valentína", "Maximilián", "Koloman", "Boris", "Terézia", "Vladimíra", "Hedviga", "Lukáš", "Kristián", "Vendelín", "Uršuľa", "Sergej", "Alojzia", "Kvetoslava", "Aurel", "Demeter", "Sabína", "Dobromila, Kevin", "Klára", "Šimon, Šimona", "Aurélia"),

			/* November */
			new Array("Denis, Denisa", "Pamiatka zosnulých", "Hubert", "Karol", "Imrich", "Renáta", "René", "Bohumír", "Teodor", "Tibor", "Martin, Maroš", "Svätopluk", "Stanislav", "Irma", "Leopold", "Agnesa", "Klaudia", "Eugen", "Alžbeta", "Félix", "Elvíra", "Cecília", "Klement", "Emília", "Katarína", "Kornel", "Milan", "Henrieta", "Vratko", "Ondrej, Andrej"),

			/* December */
			new Array("Edmund", "Bibiána", "Oldrich", "Barbora", "Oto", "Mikuláš", "Ambróz", "Marína", "Izabela", "Radúz", "Hilda", "Otília", "Lucia", "Branislava, Bronislava", "Ivica", "Albína", "Kornélia", "Sláva, Slávka", "Judita", "Dagmara", "Bohdan", "Adela", "Nadežda", "Adam a Eva", "1. Sviatok vianočný", "Štefan", "Filoména", "Ivana, Ivona", "Milada", "Dávid", "Silvester")

		);

		/* 
		** Pole nazvov mesiacov 
		*/
		var months = new Array( "Január", "Február", "Marca", "Apríl", "Máj", "Jún", "Júl", "August", "Septembr", "Oktober", "November", "December" );

		/* 
		** Pole nazvov dni
		*/
		var days = new Array( "Nedeľa", "Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok", "Sobota");

		/* 
		** Extrahovanie casti datumu: dna, poradove cislo dna, mesiaca, roku 
		*/
		var date = new Date();
		var day  = date.getDate();
		var year = date.getFullYear();
		var NoDay = date.getDay();
		var month = date.getMonth() + 1;

		/* 
		** Premena obsahujuca vypis textu 
		*/
		var text = "&#187;&nbsp;<b>";
		var displayText = days[NoDay]+", "+day+". "+months[month-1]+", "+year+" "+text+meniny[month-1][day-1]+"</b>&nbsp;&#171;";

		/* 
		** Zobrazenie textu 
		*/
		document.getElementById(id).innerHTML = displayText;

	}

  /***
   * Potvrdenie vymazanie spravy
   *
   * @param String - url adresa presmerovania v pripade ok
   * @param String - url adresa presmerovania v pripade cancel
   * @return Void
   */
	function ckeditorInit(id_name)
  {
		var typFile = 'Type=File';
		var typImage = 'Type=Image';
		var okno = '/Library/ckeditor/filemanager/browser/default/browser.html';
		var upload = '/Library/ckeditor/filemanager/connectors/php/upload.php';
		var konektor = '/Library/ckeditor/filemanager/connectors/php/connector.php';

		CKEDITOR.replace(id_name, {

				    language: 'sk',
				    uiColor: '#ffffff',
				    filebrowserBrowseUrl: okno + '?Connector=' + konektor,
				    filebrowserImageBrowseUrl: okno + '?' + typImage + '&Connector=' + konektor,
				    filebrowserUploadUrl: upload + '?' + typFile,
				    filebrowserImageUploadUrl: upload + '?' + typImage

		 });

    CKEDITOR.config.entities = false;
    CKEDITOR.config.htmlEncodeOutput = false;

		// Sirka editoru
		CKEDITOR.config.width = 900;
  }
  
  /***
   * Potvrdenie vymazanie spravy
   *
   * @param String - url adresa presmerovania v pripade ok
   * @param String - url adresa presmerovania v pripade cancel
   * @return Void
   */
	function confirmDelete(redirect_ok, redirect_cancel)
  {
    if (confirm("Naozaj chcete vymazať článok?") === true) {
      // Presmerovanie na vymazanie
      window.location = redirect_ok;
    } else {
      // Presmerovanie na povodnu stranku
      window.location = redirect_cancel;
    }
	}

  /***
   * Potvrdenie uverjenia spravy
   *
   * @param String - url adresa presmerovania v pripade ok
   * @param String - url adresa presmerovania v pripade cancel
   * @return Void
   */
	function changeStatus(redirect_ok, redirect_cancel, text)
  {
    if (confirm("Naozaj chcete " + text + " článok?") === true) {
      // Presmerovanie na vymazanie
      window.location = redirect_ok;
    } else {
      // Presmerovanie na povodnu stranku
      window.location = redirect_cancel;
    }
	}

//]]>

