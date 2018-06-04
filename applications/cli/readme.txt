

CLI UŽDUOTYS
SUTARIMAI ir KAIP TAI VEIKIA


"task.php" ir "tasks" katalogas
	
	"task.php" skirtas paleisti functionalumus aprašytus ir saugomus "tasks" kataloge
	funcionalumai gali buti saugomi "tasks/<pavadinimas>.run.php" arba "tasks/<pavadinimas>.task.class.php"
		- "<pavadinimas>.run.php" - paprasti funkcionalumai vykdomi tik iš komandinės eilutės
		- "<pavadinimas>.task.class.php" - functionalumų struktūra leidžianti vykdyti kaip atidėtus darbus
		  bei atlikti su periodinių užduočių vykdimo sistema
		  



PERIODINĖS UŽDUOTYS
	
	Periodinės užduotys yra saugomos duomenų bazėje, lentelėje "gw_crontasks". 
	Vykdomo periodiškumas ir laikas nustatomas naudojant time_match atributą
	
	
	timematch - tai periodiškai vykdomos užduoties atributas nusakantys kas kiek laiko vykdomos bus užduotys. 
	pvz  timematch "....-..-01 03:..:..#43200" nusako kad vykdoma bus 
	betkokais metais "...." match 2012,2015,9999, 
	betkokį mėnesį ".." match 01,02,...12, 
	3 valanda - "03" match 03, 
	minutę betkokia (".."), 
	sekundę betkokią (".."). 
	palyginamo kodas atrodo taip if(preg_match("/"....-..-01 03:..:../", date('Y-m-d H:i:s'))) { vykdyti_uzduoti(); }
	po "#" ženklo einantys skaičiai nusako kas kiek minučių gali būti pakartotinai vykdoma užduotis. 43200 = 60(min)*24(valandų)*30(dienų)
	Kaip patikrinti ar teisingas timematch? Ats.: Sukurti užduotį nurodyti timematch po to iš sąrašo paleisti veiksmą testTimematch
	
	Periodinių užduočių tvarkymo modulis
	www.svetaines-pavadinimas.lt/admin/lt/config/crontasks
	
DIEGIMAS
	
	1. Visi failai "cli/*.php" turi buti pažymėti execute leidimu.
	Galima pažymėti kaip paleidžiamuosius su šia komanda: "chmod a+x cli/*.php"
	
	2. Sukonfigūruoti kad operacinė sistema periodiškai vykdytų "task.php cron"
		/etc/crontab += 
		"*/5 *    * * *    www-data /kelias-iki-cms/admin/cli/task.php cron >> /kelias-iki-cms/admin/repository/.sys/logs/system.log" 



http://blog.lenss.nl/2012/05/adding-colors-to-php-cli-script-output/
