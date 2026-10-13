# Regelkatalog ImmoVerwalt

Fachliche Regeln des Altsystems – rekonstruiert aus dem Code.
Dieser Katalog ist die erste Fassung des Fachmodells und wird im Semester weiterentwickelt
(Fachmodell → Zustandsmodell → DSL → API-Spezifikation).

**Regeln formulieren:** als deutscher Satz, ohne technische Begriffe (kein Controller, kein Template, keine Tabelle).

**Status:** `bestätigt` · `widersprüchlich` · `zu klären`

| Feld | Bedeutung |
|---|---|
| Regel | fachliche Aussage in einem Satz |
| Bereich | Bestand · Vermietung · Meldungen/Aufgaben · Termine · Abrechnung |
| Fundstellen | Datei und Methode bzw. Template, an denen die Regel umgesetzt ist |
| Gegenbeobachtung | Stellen, an denen die Regel fehlt, umgangen wird oder anders umgesetzt ist |
| Status | siehe oben |
| Offene Frage | was mit dem Fachbereich zu klären ist |

---

## REGEL-01

- **Regel:** Eine Mieteinheit darf nur vermietet werden, wenn sie verfügbar ist und kein aktives Mietverhältnis besitzt.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `templates/unit/show.html.twig` – Button »Mietvertrag anlegen« nur bei Status `AVAILABLE`
  - `src/Controller/TenancyController.php::new()` – prüft den Status der Einheit
  - `src/Service/TenancyService.php::createTenancy()` – prüft vorhandene aktive Mietverhältnisse
  - `src/Repository/TenancyRepository.php::findActiveForUnit()` – Abfrage der aktiven Verträge
- **Gegenbeobachtung:** Der Controller lässt auch reservierte Einheiten zu, das Template bietet den Vertrag nur bei freien Einheiten an.
- **Status:** widersprüchlich
- **Offene Frage:** Darf für eine reservierte Einheit ein Vertrag angelegt werden – und wer darf reservieren?

---

## REGEL-02

- **Regel:** Ein Issue kann nur geschlossen werden wenn alle Unteraufgaben abgeschlossen sind, und der Abschluss muss geloggt werden
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::closeIssue()`
  - `src/Controller/IssueController::close()`
  - `src/templates/issue/show.html.twig`
- **Gegenbeobachtung:** Wenn im Template die Status verändern funktion aufgerufen wird wird IssueController::status() aufgerufen welcher keine Prüfung ausführt und das Issue direkt schließen kann, genauso bei der IssueController::bulk() funktion
- **Status:** widersprüchlich
- **Offene Frage:** Darf es ein "Force" close geben oder müssen die Prüfungen immer stattfinden ?

---

## REGEL-03

- **Regel:** Eine Meldung zu einer Einheit übernimmt automatisch die zugehörige Immobilie und den aktuellen Mieter der Einheit, wenn kein anderer Mieter angegeben ist.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt `property` aus der Einheit und ergänzt `tenant`, falls erforderlich
  - `src/Controller/IssueController.php::new()` – trägt bei der Erfassung eine Einheit aus der Anfrage ein
  - `src/Repository/TenancyRepository.php::findActiveForUnit()` – liefert das aktive Mietverhältnis für die automatische Zuordnung
- **Gegenbeobachtung:** Wenn mehrere Mietverhältnisse aktiv sind oder der Mieter nicht eindeutig bestimmt werden kann, bleibt die Zuordnung unklar.
- **Status:** bestätigt
- **Offene Frage:** Wenn eine Einheit mehrere aktive Mietverhältnisse hat, welche Person gilt dann als meldende Partei?

---

## REGEL-04

- **Regel:** Eine neu erfasste Meldung erhält automatisch einen verantwortlichen Bearbeiter, wenn kein zuständiger Mitarbeiter eingetragen ist.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt den zuständigen Mitarbeiter auf den erfassenden Benutzer
  - `src/Controller/IssueController.php::new()` – übergibt den aktuell angemeldeten Benutzer an den Dienst
- **Gegenbeobachtung:** Die automatische Zuweisung ist nur dann sinnvoll, wenn der erfassende Nutzer tatsächlich der zuständige Sachbearbeiter ist.
- **Status:** bestätigt
- **Offene Frage:** Wer darf bei einer Meldung einen anderen Bearbeiter übernehmen?

---

## REGEL-05

- **Regel:** Meldungen mit hoher Dringlichkeit werden direkt in die Prüfung übernommen und nicht erst in die normale Bearbeitung.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – bei Priorität 4 oder höher setzt er den Status auf `IN_REVIEW`
  - `src/Entity/Issue.php::PRIORITIES` – Priorität 4 wird als dringend definiert
- **Gegenbeobachtung:** Die Regel ist nur für sehr dringende Meldungen definiert; weniger dringende Fälle bleiben im normalen Ablauf.
- **Status:** bestätigt
- **Offene Frage:** Welche Meldungen gelten fachlich als dringend und müssen sofort geprüft werden?

---

## REGEL-06

- **Regel:** Eine Meldung kann nur geschlossen werden, wenn keine offenen Aufgaben mehr mit ihr verknüpft sind.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::closeIssue()` – prüft offene Aufgaben vor dem Schließen
  - `src/Repository/TaskRepository.php::countOpenForIssue()` – zählt offene Aufgaben zur Meldung
  - `src/Controller/IssueController.php::close()` – zeigt die fachliche Fehlermeldung an, wenn das Schließen nicht möglich ist
- **Gegenbeobachtung:** Die Schließung ist blockiert, obwohl der fachliche Ablauf bereits als beendet gelten könnte.
- **Status:** bestätigt
- **Offene Frage:** Dürfen außerplanmäßige Abschlussfälle trotzdem ohne offene Aufgabe geschlossen werden?

---

## REGEL-07

- **Regel:** Wenn zu einer Meldung eine Aufgabe angelegt wird, wird die Meldung in Bearbeitung gesetzt, sofern sie noch neu oder in Prüfung ist.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createTaskFromIssue()` – setzt die Meldung bei `NEW` oder `IN_REVIEW` auf `IN_PROGRESS`
  - `src/Controller/IssueController.php::addTask()` – legt die Aufgabe für die Meldung an
- **Gegenbeobachtung:** Die fachliche Frage ist, ob jede neue Aufgabe automatisch als Bearbeitungsbeginn zählt oder nur bestimmte Aufgaben.
- **Status:** bestätigt
- **Offene Frage:** Müssen alle Aufgaben einer Meldung den Status der Meldung beeinflussen oder nur die erste zugewiesene Aufgabe?

---

## REGEL-08

- **Regel:** Eine Aufgabe, die aus einer Meldung entsteht, übernimmt die Priorität der Meldung und erhält je nach Dringlichkeit eine passende Frist.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createTaskFromIssue()` – kopiert Priorität und setzt die Frist abhängig von der Dringlichkeit
  - `src/Entity/Issue.php::PRIORITIES` – definiert die Prioritätsstufen
- **Gegenbeobachtung:** Die Frist ist nur als grobe Arbeitsregel modelliert und nicht als fachlich verbindliche Vertragsfrist.
- **Status:** bestätigt
- **Offene Frage:** Welche Fristen sind für den Fachbereich verbindlich, wenn eine Meldung mit hoher Priorität entsteht?

---

## REGEL-09

- **Regel:** Jede Statusänderung einer Meldung wird mit dem früheren Status, dem neuen Status, dem Bearbeiter und einem Hinweis dokumentiert.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Controller/IssueController.php::status()` – speichert bei jeder Änderung einen Statuswechsel
  - `src/Entity/IssueStatusChange.php` – modelliert die Historie des Zustandswechsels
  - `src/Service/IssueService.php::createIssue()` – legt beim Anlegen ebenfalls einen Statuswechsel an
- **Gegenbeobachtung:** Die Historisierung ist wichtig, aber die genaue Regel für die Freigabe von Kommentaren ist noch offen.
- **Status:** bestätigt
- **Offene Frage:** Muss ein Kommentar bei jeder Statusänderung zwingend erforderlich sein oder nur optional?

---

## REGEL-10

- **Regel:** Eine Meldung erhält eine eindeutige laufende Nummer, die bei der Erfassung automatisch vergeben wird.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt die Nummer vor dem Speichern
  - `src/Repository/IssueRepository.php::nextNumber()` – erzeugt die nächste Nummer
- **Gegenbeobachtung:** Die Nummerierung ist vorhanden, aber das Format ist nur in diesem System festgelegt und nicht fachlich abgestimmt.
- **Status:** bestätigt
- **Offene Frage:** Ist die laufende Nummer eine interne Verwaltungsnummer oder eine nach außen sichtbare Kundennummer?

---

## REGEL-11

- **Regel:** Eine Meldung kann nur dann eine Aufgabe erhalten, wenn ein aussagekräftiger Titel für die Aufgabe angegeben ist.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Controller/IssueController.php::addTask()` – prüft, ob der Titel leer ist
  - `src/Service/IssueService.php::createTaskFromIssue()` – erzeugt die Aufgabe nur mit gültigen Angaben
- **Gegenbeobachtung:** Die fachliche Regel ist klar definiert, aber in der Praxis könnte ein kurzer Standardtext sinnvoll sein.
- **Status:** bestätigt
- **Offene Frage:** Muss der Titel für alle Aufgaben immer manuell eingegeben werden oder gibt es Standardtitel nach Kategorie?

---

## REGEL-12

- **Regel:** Eine Einheit darf zu einem bestimmten Zeitraum nur ein aktives Mietverhältnis besitzen.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – prüft vorhandene aktive Mietverhältnisse
  - `src/Repository/TenancyRepository.php::findActiveForUnit()` – liefert aktive Verträge für dieselbe Einheit
  - `src/Entity/Tenancy.php::unit` – verbindet das Mietverhältnis mit genau einer Einheit
- **Gegenbeobachtung:** Die fachliche Prüfung existiert, aber die Regel ist nicht als eigenständiger fachlicher Sachverhalt im Konzeptmodell sichtbar.
- **Status:** bestätigt
- **Offene Frage:** Was gilt fachlich bei Mehrfachvermietung aus einem Übergangsfall oder bei einer vorübergehenden Freistellung?

---

## REGEL-13

- **Regel:** Ein Mietverhältnis hat einen gültigen Zeitrahmen: der Beginn muss vor dem Ende liegen, wenn ein Ende festgelegt ist.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – prüft überlappende Zeiträume und das Verhältnis von Beginn und Ende
  - `src/Entity/Tenancy.php::startDate` – Vertragsbeginn
  - `src/Entity/Tenancy.php::endDate` – Vertragsende
- **Gegenbeobachtung:** Die fachliche Regel ist vorhanden, aber die zeitlichen Zustandsgrenzen sind nicht als eigenes Konzept im Modell abgebildet.
- **Status:** bestätigt
- **Offene Frage:** Gilt ein Ende ohne festen Termin als offen oder als unbegrenzter Vertrag?

---

## REGEL-14

- **Regel:** Ein aktives Mietverhältnis muss mindestens einen Mieter umfassen, und der letzte Mieter kann nicht ohne Weiteres entfernt werden.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::tenants` – Sammlung der Mietparteien
  - `src/Service/TenancyService.php::activate()` – prüft, ob mindestens ein Mieter vorhanden ist
  - `src/Service/TenancyService.php::removeTenant()` – verhindert das Entfernen des letzten Mieters eines aktiven Vertrags
- **Gegenbeobachtung:** Die fachliche Beziehung zwischen Mietverhältnis und Mietpartei ist erkennbar, aber die Rolle des Mieters ist nicht separat als eigener fachlicher Begriff modelliert.
- **Status:** bestätigt
- **Offene Frage:** Ist ein Mietverhältnis immer nur an natürliche Personen gebunden oder auch an juristische Personen?

---

## REGEL-15

- **Regel:** Ein Mietverhältnis dokumentiert die Kündigung separat vom Status und setzt die Einheit entsprechend auf den passenden Zustand bei der Rückgabe.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::noticeGiven` – zusätzliche fachliche Kennzeichnung der Kündigung
  - `src/Entity/Tenancy.php::noticeDate` – Datum der Kündigung
  - `src/Service/TenancyService.php::terminateImmediately()` – setzt den Status und den Einheitstatus
  - `src/Service/TenancyService.php::recordHandover()` – bei Rückgabe wird die Einheit frei oder in Renovierung gesetzt
- **Gegenbeobachtung:** Der fachliche Begriff „Kündigung“ ist im Modell doppelt vorhanden: einmal als Status, einmal als eigenes Kennzeichen.
- **Status:** widersprüchlich
- **Offene Frage:** Ist die Kündigung ein eigener fachlicher Zustand oder nur ein Zusatzmerkmal zum bestehenden Vertragstatus?

---

## REGEL-16

- **Regel:** Die wirtschaftlichen Inhalte eines Mietverhältnisses werden im Vertrag festgehalten und bleiben für die Laufzeit des Vertrages verbindlich.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::coldRent` – Kaltmiete laut Vertrag
  - `src/Entity/Tenancy.php::serviceChargeAdvance` – Vorauszahlung für Betriebskosten
  - `src/Entity/Tenancy.php::deposit` – Kaution
  - `src/Service/TenancyService.php::createTenancy()` – übernimmt Werte aus der Einheit, falls nicht separat angegeben
- **Gegenbeobachtung:** Die Mietwerte sind an der Einheit als Standardwerte hinterlegt, aber im Vertrag werden sie separat fixiert.
- **Status:** bestätigt
- **Offene Frage:** Darf die Miete oder die Nebenkostenvorauszahlung im Laufe des Vertrags geändert werden oder bleibt sie für die gesamte Vertragslaufzeit unverändert?

---

## REGEL-17

- **Regel:** Einzug und Auszug werden als Übergabeereignisse dokumentiert und sind Teil des Lebenszyklus eines Mietverhältnisses.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::handovers` – Sammlung der Übergaben
  - `src/Service/TenancyService.php::recordHandover()` – dokumentiert Einzug bzw. Auszug
  - `src/Service/TenancyService.php::activate()` – erzeugt bei Aktivierung die Übergabe zum Einzug
- **Gegenbeobachtung:** Die Übergabe ist fachlich wichtig, aber der Begriff ist im fachlichen Modell noch nicht als eigener Vorgang mit eigener Bedeutung ausformuliert.
- **Status:** bestätigt
- **Offene Frage:** Welche Übergabeschritte müssen fachlich zwingend dokumentiert werden – nur Einzug und Auszug oder auch Zwischenzustände?

---

## REGEL-18

- **Regel:** Ein Mietverhältnis hat eine eindeutige Vertragsnummer, die den Vertrag fachlich identifiziert.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::contractNumber` – fachlich eindeutige Vertragsnummer
  - `src/Repository/TenancyRepository.php::nextContractNumber()` – erzeugt die nächste laufende Nummer
- **Gegenbeobachtung:** Die Nummer ist fachlich sinnvoll, aber sie ist technisch in der Datenbank als eindeutiger String abgebildet und nicht als eigenes fachliches Dokumentmodell ausformuliert.
- **Status:** bestätigt
- **Offene Frage:** Ist die Vertragsnummer nur intern verwaltbar oder muss sie für den Mieter sichtbar sein?
