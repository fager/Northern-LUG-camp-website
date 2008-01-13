<?php

$PAGE['rechnung']['name'] = "Rechnung";
$PAGE['rechnung']['navilevel'] = 2;
$PAGE['rechnung']['login_required'] = 1;
$PAGE['rechnung']['phpclass'] = 'HtmlPage_rechnung';
$PAGE['rechnung']['parent'] = 'mycamp';

class HtmlPage_rechnung extends HtmlPage {

	function getContent() {
		global $_SESSION;
		global $CURRENT_EVENT_ID;


		// Pruefen ob etwas geloescht werden soll
		$a = http_get_var('a');
		$accountartikelid = http_get_var('accountartikelid');
		$eventid = http_get_var('eventid');

		if($a=='d' && is_numeric($accountartikelid)) {
			$SQLdel = "DELETE FROM event_account_artikel WHERE accountartikelid=".$accountartikelid." AND ";
			$SQLdel .= " accountid=".$_SESSION['_accountid'];
			my_query($SQLdel);
		}else if($a=='d' && is_numeric($eventid)) {
			$anmeldungid = http_get_var('anmeldungid');
			if(is_numeric($anmeldungid)) {
				$SQLdel = "DELETE FROM event_anmeldung_event WHERE eventid=".$eventid." AND anmeldungid=".$anmeldungid;
				my_query($SQLdel);
			}
		}

		// Feststellen fuer welches Event wir grade anmelden
		$ceventid = 0;
		if(isset($CURRENT_EVENT_ID) && is_numeric($CURRENT_EVENT_ID))
			$ceventid = $CURRENT_EVENT_ID;

   		$ret = '
				<h1>Kosten</h1>
				<p>Auf dieser Seite kannst Du sehen, welche Anmeldungen und Eink&auml;ufe Du get&auml;tigt hast und ob diese bereits bezahlt sind.</p>
			';

		$zuzahlen = 0;
		if(is_numeric($_SESSION['_accountid'])) {
			// Abfragen welche Personen angemeldet sind
			$SQL1 = "SELECT a.* ";
			$SQL1 .= " FROM event_anmeldung a ";
			$SQL1 .= " LEFT JOIN event_anmeldung_event ae ON a.anmeldungid=ae.anmeldungid ";
			$SQL1 .= " WHERE a.accountid=".$_SESSION['_accountid']." AND ae.eventid=".$ceventid;
			$res1 = my_query($SQL1);
			if($res1) {
				while($row1 = mysql_fetch_assoc($res1) ) { // alle Personen durchgehen
					$anmeldungid = $row1['anmeldungid'];

					$ret .= '<h2>'.$row1['vorname']." " . $row1['nachname'].'</h2>';


					// Auflisten, welche Events alle gebucht wurden
					$SQL2 = "SELECT e.* ";
					$SQL2 .= " ,IF(e.buchanfang<NOW() AND e.buchende>NOW() AND parent IS NOT NULL,1,0) AS editable ";
					$SQL2 .= " FROM event_event e ";
					$SQL2 .= " LEFT JOIN event_anmeldung_event ae ON e.eventid=ae.eventid ";
					$SQL2 .= " WHERE ae.anmeldungid=".$anmeldungid;
					$res2 = my_query($SQL2);
					if($res2) {
						if(mysql_num_rows($res2)>0) {
							$ret .= '<table class="datatable1">
							<caption>Anmeldungen</caption>
							<thead>
								<tr>
									<th>Event</th>
									<th>Einzelpreis</th>
									<th>Bezahlstatus</th>
								</tr>
							</thead>
							<tbody>
							';

							while($row2 = mysql_fetch_assoc($res2)) {
								$bezahlstatus = "-";

								$cmd = '';
								if($bezahlstatus=='-' && $row2['editable']) {
									$cmd = '<a href="?p=rechnung&anmeldungid='.$anmeldungid.'&eventid='.$row2['eventid'].'&a=d">abmelden</a>';
								}

								if($bezahlstatus = "-") {
									// wenn noch nicht bezahlt, dann zum gesamtbetrag zurechnen
									$zuzahlen += $row2['charge'];
								}
								$ret .= '
									<tr>
										<td>'.$row2['name'].'</td>
										<td style="text-align:right;">'.number_format($row2['charge'],2,',','.').' &euro;</td>
										<td style="text-align:center;">'.$bezahlstatus.'</td>
										<td>'.$cmd.'</td>
									</tr>
								';
							} // while
							$ret .= '</tbody></table>';
						} // if num_rows
						mysql_free_result($res2);
					} // if res2

				} // while personen
				mysql_free_result($res1);
			} // if res1


			// Auflisten, welche Artikel gekauft wurden.
			$SQL2 = "SELECT aa.accountartikelid,a.*,aa.groesse,aa.anzahl,(aa.anzahl*a.preis) AS gesamtpreis ";
			$SQL2 .= " ,IF(a.kaufab<NOW() AND a.kaufbis>NOW(),1,0) AS editable ";
			$SQL2 .= " FROM event_account_artikel aa ";
			$SQL2 .= " LEFT JOIN event_artikel a ON aa.artikelid=a.artikelid ";
			$SQL2 .= " WHERE aa.accountid=".$_SESSION['_accountid'];
			$res2 = my_query($SQL2);
			if($res2) {
				if(mysql_num_rows($res2)>0) {
					$ret .= '
						<table class="datatable1">
						<caption>Bestellungen</caption>
							<thead>
								<tr>
									<th>Artikel</th>
									<th>Gr&ouml;sse</th>
									<th>Anzahl</th>
									<th>Einzelpreis</th>
									<th>Gesamtpreis</th>
									<th>Bezahlstatus</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
					';
					while($row2 = mysql_fetch_assoc($res2)) {
						$bezahlstatus='-';
						$groesse = '-';
						if(isset($row2['groesse']) && $row2['groesse']!='')
							$groesse = $row2['groesse'];

						$cmd = '';
						if($bezahlstatus=='-' && $row2['editable']) {
							$cmd = '<a href="?p=rechnung&accountartikelid='.$row2['accountartikelid'].'&a=d">l&ouml;schen</a>';
						}

						if($bezahlstatus = "-") {
							// wenn noch nicht bezahlt, dann zum gesamtbetrag zurechnen
							$zuzahlen += $row2['gesamtpreis'];
						}
						$ret .= '
							<tr>
								<td>'.$row2['name'].'</td>
								<td style="text-align:center;">'.$groesse.'</td>
								<td style="text-align:center;">'.$row2['anzahl'].'</td>
								<td style="text-align:right;">'.number_format($row2['preis'],2,',','.').' &euro;</td>
								<td style="text-align:right;">'.number_format($row2['gesamtpreis'],2,',','.').' &euro;</td>
								<td style="text-align:center;">'.$bezahlstatus.'</td>
								<td>'.$cmd.'</td>
							</tr>
						';
					}
					$ret .= '
						</tbody>
						</table>
					';
				} // if num_rows res2
				mysql_free_result($res2);
			} // if res2 (Auflistung der Artikel)

			$ret .= '
			<p>
				Insgesamt erwarten wir von Dir noch eine &Uuml;berweise in H&ouml;he von <b>'.number_format($zuzahlen,2,',','.').' &euro;</b>. Damit die Anmeldung g&uuml;ltig ist, muss die <b>&Uuml;berweisung bis zum 23.04.08</b> bei uns eingegangen sein.
			</p>
			';
		} // if is_numeric accountid
		$ret .= '
		<h1>Bankverbindung</h1>
		<p>
		Bitte &uuml;berweise den noch ausstehenden Betrag auf das folgende Konto:
		</p>
		<p>
			<address>
			Kontoinhaber: LUG Flensburg e.V.<br/>
			Bank: Union Bank AG<br/>
			BLZ: 215 201 00<br/>
			Kto: 16632<br/>
			</address>
			Verwendungszweck: LC 2008 und Nickname
		</p>
		<p>
			F&uuml;r Sammel&uuml;berweisungen einer LUG oder aus dem Ausland setzt Euch bitte mit unserem Finanzmeister in Verbindung, den Ihr per Mail an
			<a href="mailto:kasse@lug-camp-2008.de">kasse@lug-camp-2008.de</a> erreicht.
		</p>
		';
		return $ret;
	}

}


?>
