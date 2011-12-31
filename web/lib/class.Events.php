<?php

/**
 * Access-Class for Events
 */
class Events  {

	const ALL_EVENTS = 1;
	const FILTER_EVENTS = 2;

	private $pdo = null;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

	/**
	 * Get list of Events for specified domain without child-events
	 */
	public function getRootEvents($domainid) {
		$ret = array();
		try {
			$SQL = 'SELECT e.*,de.domainid FROM event_event e 
			LEFT JOIN domain_event de ON e.eventid=de.eventid
			WHERE e.hidden=0 AND e.parent IS NULL AND de.domainid=? ORDER BY e.sort';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($domainid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		print_r($ret);
		return $ret;
	}

	/**
	 * Get list of all Events for specified domain
	 */
	public function getEvents($domainid,$filter = Events::FILTER_EVENTS) {
		$ret = array();
		try {
			$SQL  = 'SELECT e.*,de.domainid,COUNT(eae.anmeldungid) AS curregistrations ';
			$SQL .= ' FROM event_event e ';
			$SQL .= ' LEFT JOIN domain_event de ON e.eventid=de.eventid';
			$SQL .= ' LEFT JOIN event_anmeldung_event eae ON e.eventid=eae.eventid ';
			$SQL .= ' WHERE e.hidden=0 AND de.domainid=? ';
			if( $filter == Events::FILTER_EVENTS ) {
				$SQL .= ' AND buchanfang <= NOW() AND buchende >= NOW() ';
			}
			$SQL .= ' GROUP BY e.eventid ';
			$SQL .= ' ORDER BY e.sort';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($domainid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}

	/**
	 * Get a Event by ID
	 */
	public function getEventById($eventid) {
		$ret = null;
		try {
			$SQL = 'SELECT e.*,de.domainid FROM event_event e 
			LEFT JOIN domain_event de ON e.eventid=de.eventid
			WHERE e.hidden=0 AND e.eventid=? ORDER BY e.sort';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($eventid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}

	/**
	 * Insert new Registration in Tabele event_anmeldung and event_anmeldung_event
	 */
	public function addEventRegistration($data) {

		if( ! isset($data['bemerkung']) ) {
			$data['bemerkung'] = '';
		}
		$insert_id = null;

		// Prepare named parameters for INSERT
		$pairs = Array();
		$pairs[':accountid'] = $data['accountid'];
		$pairs[':lugid'] = $data['lugid'];
		$pairs[':vorname'] = $data['vorname'];
		$pairs[':nachname'] = $data['nachname'];
		$pairs[':strasse'] = $data['strasse'];
		$pairs[':hausnr'] = $data['haus'];
		$pairs[':plz'] = $data['plz'];
		$pairs[':ort'] = $data['ort'];
		$pairs[':landid'] = $data['landid'];
		$pairs[':email'] = $data['email'];
		$pairs[':gebdat'] = $data['geb_y'] . '-' . $data['geb_m'] . '-' . $data['geb_d'];
		$pairs[':vegetarier'] = $data['vegetarier'];
		$pairs[':arrival'] = $data['anreise'];
		$pairs[':ankunft'] = $data['ankunft'];
		$pairs[':abfahrt'] = $data['abfahrt'];
		$pairs[':bemerkung'] = $data['bemerkung'];

		try {
			// Create databasenetry with person-related registration-Data
			$SQL = 'INSERT INTO event_anmeldung (accountid,lugid,vorname,nachname,strasse,hausnr,plz,ort,landid,email,gebdat,vegetarier,arrival,ankunft,abfahrt,bemerkung) ';
			$SQL .= 'VALUES (:accountid,:lugid,:vorname,:nachname,:strasse,:hausnr,:plz,:ort,:landid,:email,DATE(:gebdat),:vegetarier,:arrival,:ankunft,:abfahrt,:bemerkung) ';
			$st = $this->pdo->prepare($SQL);
			$st->execute($pairs);
			$insert_id = $this->pdo->lastInsertId();
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}

		if( $insert_id != null && isset($data['events']) && is_array($data['events']) ) {
			// register the new person with each selected event...
			try {
				$SQL = 'INSERT INTO event_anmeldung_event (anmeldungid,eventid,accountid) VALUES (?,?,?)';
				$st = $this->pdo->prepare($SQL);
				foreach( $data['events'] as $eventid ) {
					$st->execute( array($insert_id,$eventid,$data['accountid']) );
				}
				$st->closeCursor();
			} catch ( PDOException $e ) {
				print $e;
			}
		}
		return $insert_id;
	}

	public function getEventRegistrationsForAccount($domainid,$accountid) {
		$ret = array();

		try{
			$SQL = 'SELECT eae.bezahlt, ee.name as eventname, ee.charge as eventkosten, ea.anmeldungid '
				.'FROM event_anmeldung_event eae '
				.'LEFT JOIN event_anmeldung ea ON ea.anmeldungid = eae.anmeldungid ' 					.'LEFT JOIN event_event ee ON ee.eventid = eae.eventid '
				.'LEFT JOIN domain_event de ON de.eventid = eae.eventid '
				.'WHERE de.domainid = ? AND eae.accountid = ?';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($domainid,$accountid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}

		return $ret;
	}

	public function getEventRegistration($anmeldungid) {
		$ret = array();

		try{
			$SQL = 'SELECT ';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($domainid,$accountid));
			$ret = $st->fetch(PDO::FETCH_ASSOC);
			$st->closeCursor();
		

		} catch (PDOException $e) {
			print $e;
		}

		return $ret;
	}

	public function getBoughtArtikelForAccount($domainid, $accountid) {
		$ret = array();

		try{
			$SQL = 'SELECT ea.name, eaa.groesse, eaa.anzahl, (eaa.anzahl*ea.preis) as kosten, eaa.bezahlt '
				.'FROM event_account_artikel eaa '
				.'LEFT JOIN event_artikel ea ON ea.artikelid = eaa.artikelid '
				.'WHERE eaa.accountid = ?';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($accountid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}

		return $ret;
	}
}

?>
