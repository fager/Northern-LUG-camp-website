<?php

/**
 * Access-Class for Events
 */
class Events  {

	private $pdo = null;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

	/**
	 * Get list of Events for specified domain
	 */
	public function getRootEvents($domainid) {
		$ret = array();
		try {
			$SQL = 'SELECT e.*,de.domainid FROM event_event e 
			LEFT JOIN domain_event de ON e.eventid=de.eventid
			WHERE e.hidden=0 AND e.parent IS NULL AND de.domainid=?';
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

}

?>
