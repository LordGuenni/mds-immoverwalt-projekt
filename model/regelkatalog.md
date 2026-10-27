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

- **Regel:** Eine Meldung darf nur geschlossen werden, wenn alle ihre Aufgaben erledigt oder abgebrochen sind.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::closeIssue()` – bricht ab, wenn noch offene Aufgaben bestehen
  - `src/Repository/TaskRepository.php::countOpenForIssue()` – zählt Aufgaben, die weder `DONE` noch `CANCELLED` sind
  - `src/Controller/IssueController.php::close()` – zeigt die Fehlermeldung an
  - `templates/issue/show.html.twig` – Button »Meldung schließen« nur, wenn die Meldung weder geschlossen noch abgelehnt ist
- **Gegenbeobachtung:**
  - `src/Controller/IssueController.php::status()` setzt jeden Status ohne Prüfung, auch »Geschlossen« bei offenen Aufgaben; die Auswahlliste in `templates/issue/show.html.twig` bietet »Geschlossen« an.
  - `src/Controller/IssueController.php::bulk()` schließt mehrere Meldungen ohne Prüfung.
  - `src/Controller/TaskController.php::reopen()` und das Statusfeld in `src/Form/TaskType.php` öffnen Aufgaben einer bereits geschlossenen Meldung wieder; danach hat eine geschlossene Meldung offene Aufgaben.
  - `closeIssue()` schreibt keinen Eintrag in die Statushistorie (siehe REGEL-09).
- **Status:** widersprüchlich
- **Offene Frage:** Darf eine Meldung mit offenen Aufgaben in Ausnahmefällen geschlossen werden (z. B. durch Admin), und was passiert mit der Meldung, wenn eine ihrer Aufgaben wiedereröffnet wird?

---

## REGEL-03

- **Regel:** Eine Meldung zu einer Mieteinheit gehört immer zur Liegenschaft dieser Mieteinheit; ist kein meldender Mieter angegeben, wird ein Mieter des aktuellen Mietverhältnisses eingetragen.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt die Liegenschaft aus der Mieteinheit und ergänzt den Mieter
  - `src/Controller/IssueController.php::new()` – übernimmt Mieteinheit und Liegenschaft aus dem Aufruf
  - `src/Repository/TenancyRepository.php::findActiveForUnit()` – liefert aktive und gekündigte Mietverhältnisse
- **Gegenbeobachtung:**
  - Eingetragen wird der erste Mieter des ersten gefundenen Mietverhältnisses; bei mehreren Mietern ist die Auswahl zufällig.
  - Im Formular gewählte Liegenschaft wird beim Erfassen stillschweigend überschrieben; beim Bearbeiten (`IssueController::edit()`) dagegen nicht, dort können Liegenschaft und Mieteinheit auseinanderlaufen.
- **Status:** widersprüchlich
- **Offene Frage:** Welcher Mieter gilt bei mehreren Mietern als meldende Person? Darf eine Meldung nachträglich einer Mieteinheit einer anderen Liegenschaft zugeordnet werden?

---

## REGEL-04

- **Regel:** Eine neue Meldung ohne Bearbeiter erhält den erfassenden Mitarbeiter als Bearbeiter.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt den Bearbeiter auf den erfassenden Benutzer
  - `src/Controller/IssueController.php::new()` – übergibt den angemeldeten Benutzer
- **Gegenbeobachtung:** keine; über `IssueController::edit()` kann jeder angemeldete Benutzer den Bearbeiter ändern.
- **Status:** bestätigt
- **Offene Frage:** Wer darf einer Meldung einen anderen Bearbeiter zuweisen?

---

## REGEL-05

- **Regel:** Eine dringende Meldung (Priorität »dringend«) wird bei der Erfassung sofort in Prüfung genommen.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt bei Priorität 4 den Meldungsstatus `IN_REVIEW`
  - `src/Entity/Issue.php::PRIORITIES` – Priorität 4 heißt »dringend«
- **Gegenbeobachtung:**
  - Wird die Priorität später über `IssueController::edit()` auf »dringend« erhöht, ändert sich der Status nicht.
  - Für die Fälligkeit von Aufgaben gilt eine andere Schwelle (ab »hoch«, siehe REGEL-08).
- **Status:** zu klären
- **Offene Frage:** Gilt die Regel nur bei der Erfassung oder bei jeder Erhöhung auf »dringend«? Ist »hoch« ebenfalls dringend?

---

## REGEL-06

- **Zusammengeführt mit REGEL-02** (beschrieb dieselbe Regel; siehe Logbuch). Die Nummer bleibt frei, damit Verweise eindeutig bleiben.

---

## REGEL-07

- **Regel:** Wird zu einer neuen oder in Prüfung befindlichen Meldung eine Aufgabe angelegt, ist die Meldung danach in Bearbeitung.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createTaskFromIssue()` – setzt die Meldung von `NEW` oder `IN_REVIEW` auf `IN_PROGRESS`
  - `src/Controller/IssueController.php::addTask()` – legt die Aufgabe aus der Meldung an
- **Gegenbeobachtung:**
  - Eine Aufgabe, die über »Neue Aufgabe« (`TaskController::new()` → `TaskService::save()`) mit Bezug zur Meldung angelegt wird, ändert den Meldungsstatus nicht.
  - Der Statuswechsel wird nicht in der Statushistorie festgehalten (siehe REGEL-09).
- **Status:** widersprüchlich
- **Offene Frage:** Soll jede Aufgabe zu einer Meldung die Meldung in Bearbeitung setzen, unabhängig vom Weg, auf dem sie angelegt wird?

---

## REGEL-08

- **Regel:** Eine Aufgabe aus einer Meldung übernimmt deren Priorität und ist bei Priorität »hoch« oder »dringend« in 2 Tagen, sonst in 14 Tagen fällig.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createTaskFromIssue()` – übernimmt die Priorität und setzt die Fälligkeit (`+2 days` ab Priorität 3, sonst `+14 days`)
  - `src/Entity/Issue.php::PRIORITIES` – Prioritätsstufen
- **Gegenbeobachtung:**
  - Über »Neue Aufgabe« (`TaskController::new()`) gibt es weder übernommene Priorität noch Fälligkeit.
  - Ändert sich die Priorität der Meldung später, bleibt die Fälligkeit der Aufgabe unverändert.
- **Status:** bestätigt
- **Offene Frage:** Sind 2 bzw. 14 Tage verbindliche Fristen des Fachbereichs?

---

## REGEL-09

- **Regel:** Jeder Statuswechsel einer Meldung wird mit altem und neuem Status, Zeitpunkt, Bearbeiter und Kommentar in der Statushistorie festgehalten.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Controller/IssueController.php::status()` – schreibt bei jedem manuellen Wechsel einen Eintrag
  - `src/Service/IssueService.php::createIssue()` – schreibt den ersten Eintrag beim Erfassen
  - `src/Entity/IssueStatusChange.php` – Eintrag der Statushistorie
- **Gegenbeobachtung:** Drei Wege ändern den Status **ohne** Eintrag in der Statushistorie:
  - `src/Service/IssueService.php::closeIssue()` (Schließen)
  - `src/Service/IssueService.php::createTaskFromIssue()` (in Bearbeitung durch neue Aufgabe)
  - `src/Controller/IssueController.php::bulk()` (Sammeländerung)
- **Status:** widersprüchlich
- **Offene Frage:** Muss bei jedem Statuswechsel ein Kommentar angegeben werden? (Im Altsystem ist er optional.)

---

## REGEL-10

- **Regel:** Jede Meldung erhält beim Erfassen eine eindeutige, fortlaufende Meldungsnummer.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt die Nummer
  - `src/Repository/IssueRepository.php::nextNumber()` – Nummer = `ME-` + (2000 + Anzahl Meldungen + 1)
  - `src/Entity/Issue.php::issueNumber` – eindeutig
- **Gegenbeobachtung:** Die Nummer wird aus der Anzahl der Meldungen berechnet; werden zwei Meldungen gleichzeitig erfasst, erhalten beide dieselbe Nummer und die zweite scheitert an der Eindeutigkeit. Aufgaben (`TaskRepository::nextNumber()`, `AU-`) werden genauso nummeriert.
- **Status:** bestätigt
- **Offene Frage:** Ist die Meldungsnummer nach außen sichtbar (z. B. auf der Mitteilung an den Mieter)?

---

## REGEL-11

- **Regel:** Jede Aufgabe braucht einen Titel.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Controller/IssueController.php::addTask()` – lehnt einen leeren Titel ab
  - `src/Entity/Task.php::title` – Pflichtfeld im Formular »Neue Aufgabe«
- **Gegenbeobachtung:** keine
- **Status:** bestätigt
- **Offene Frage:** Soll es Standardtitel je Kategorie der Meldung geben?

---

## REGEL-12

- **Regel:** Die Laufzeiten der Mietverhältnisse einer Mieteinheit dürfen sich nicht überschneiden.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – lehnt überschneidende Laufzeiten ab
  - `src/Repository/TenancyRepository.php::findOverlapping()` – sucht Mietverhältnisse derselben Mieteinheit mit überschneidender Laufzeit
- **Gegenbeobachtung:**
  - `findOverlapping()` ignoriert Entwürfe: Für eine Mieteinheit können mehrere Entwürfe mit gleicher Laufzeit angelegt werden.
  - `TenancyService::activate()` prüft die Überschneidung nicht erneut; zwei überlappende Entwürfe lassen sich nacheinander aktivieren.
  - Unterschied zu REGEL-01: REGEL-01 betrifft den Einheitenstatus bei der Anlage, REGEL-12 die zeitliche Überschneidung.
- **Status:** widersprüchlich
- **Offene Frage:** Darf es für eine Mieteinheit mehrere Entwürfe gleichzeitig geben (z. B. für mehrere Interessenten)?

---

## REGEL-13

- **Regel:** Das Ende eines Mietverhältnisses liegt nicht vor seinem Beginn; ohne Ende ist es unbefristet.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – lehnt ein Ende vor dem Beginn ab
  - `templates/tenancy/show.html.twig` – zeigt ohne Ende »unbefristet«
- **Gegenbeobachtung:** Bei Kündigung (`TenancyController::terminate()`) und Aufhebung (`TenancyService::terminateImmediately()`) wird das Ende ohne Prüfung gesetzt; ein Stichtag vor dem Beginn wird angenommen.
- **Status:** widersprüchlich
- **Offene Frage:** Darf ein Mietverhältnis vor seinem Beginn aufgehoben werden, und welches Ende gilt dann?

---

## REGEL-14

- **Regel:** Ein Mietverhältnis kann nur mit mindestens einem Mieter aktiviert werden, und einem laufenden Mietverhältnis darf der letzte Mieter nicht entfernt werden.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::activate()` – verlangt mindestens einen Mieter
  - `src/Service/TenancyService.php::removeTenant()` – verhindert das Entfernen des letzten Mieters bei aktivem Mietverhältnis
- **Gegenbeobachtung:** `removeTenant()` prüft nur den Zustand »aktiv«; bei einem gekündigten Mietverhältnis kann der letzte Mieter entfernt werden.
- **Status:** widersprüchlich
- **Offene Frage:** Kann ein Mieter auch eine Firma sein? Darf bei einem gekündigten Mietverhältnis der letzte Mieter entfernt werden?

---

## REGEL-15

- **Regel:** Ob ein Mietverhältnis gekündigt ist, ergibt sich aus seinem Zustand und dem Kündigungsdatum.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::status` – Zustand `NOTICE_GIVEN` (gekündigt)
  - `src/Entity/Tenancy.php::noticeDate` – Kündigungsdatum
  - `src/Entity/Tenancy.php::noticeGiven` – zusätzliches Kennzeichen (Ticket #213)
- **Gegenbeobachtung:**
  - Die Kündigung ist doppelt abgelegt: als Zustand und als Kennzeichen `noticeGiven`.
  - Die ordentliche Kündigung (`TenancyController::terminate()`) setzt das Kennzeichen **nicht**; nur die Aufhebung (`TenancyService::terminateImmediately()`) setzt es.
  - `templates/tenancy/show.html.twig` blendet »Kündigung erfassen« nur aus, wenn das Kennzeichen gesetzt ist; der Controller selbst prüft nur den Zustand.
- **Status:** widersprüchlich
- **Entscheidung:** Das Kennzeichen wird nicht ins Modell übernommen; maßgeblich sind Zustand und Kündigungsdatum (siehe Logbuch).
- **Offene Frage:** Wofür wurde das Kennzeichen mit Ticket #213 eingeführt?

---

## REGEL-16

- **Regel:** Kaltmiete, Nebenkostenvorauszahlung und Kaution werden beim Anlegen des Mietverhältnisses festgelegt; fehlen sie, gelten Angebotsmiete und Vorauszahlung der Mieteinheit und eine Kaution von drei Kaltmieten.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – übernimmt fehlende Werte aus der Mieteinheit, Kaution = 3 × Kaltmiete
  - `src/Controller/TenancyController.php::new()` – füllt das Formular mit den Werten der Mieteinheit vor
  - `src/Entity/Tenancy.php::coldRent`, `serviceChargeAdvance`, `deposit`
- **Gegenbeobachtung:** keine; es gibt keine Route, mit der die Konditionen eines Mietverhältnisses später geändert werden, und eine Änderung der Angebotsmiete wirkt nicht auf bestehende Mietverhältnisse.
- **Status:** bestätigt
- **Offene Frage:** Wie werden Mieterhöhungen oder angepasste Vorauszahlungen abgebildet?

---

## REGEL-17

- **Regel:** Zu jedem Mietverhältnis werden Einzug und Auszug als Übergabe mit Datum, Zählerständen, Schlüsselanzahl und Mängeln protokolliert.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::recordHandover()` – speichert die Übergabe
  - `src/Service/TenancyService.php::activate()` – plant den Termin für den Einzug
  - `src/Controller/TenancyController.php::terminate()`, `src/Service/TenancyService.php::terminateImmediately()` – planen den Termin für den Auszug
- **Gegenbeobachtung:** Die Art der Übergabe ist im Formular frei wählbar; mehrere Einzüge oder Auszüge zu einem Mietverhältnis sind möglich.
- **Status:** bestätigt
- **Offene Frage:** Darf es je Mietverhältnis mehr als einen Einzug bzw. Auszug geben?

---

## REGEL-18

- **Regel:** Jedes Mietverhältnis hat eine eindeutige Vertragsnummer, die beim Anlegen vergeben wird.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Entity/Tenancy.php::contractNumber` – eindeutig
  - `src/Repository/TenancyRepository.php::nextContractNumber()` – Nummer = `MV-` + (1000 + Anzahl Mietverhältnisse + 1)
- **Gegenbeobachtung:** Wie bei REGEL-10 aus der Anzahl berechnet; gleichzeitiges Anlegen erzeugt doppelte Nummern.
- **Status:** bestätigt
- **Offene Frage:** Ist die Vertragsnummer für Mieter sichtbar?

---

## REGEL-19

- **Regel:** Ein aktives Mietverhältnis kann gekündigt werden; es endet drei Monate nach dem Kündigungsdatum, und die Rückgabe wird für diesen Tag geplant.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Controller/TenancyController.php::terminate()` – nur bei aktivem Mietverhältnis; Ende = Kündigungsdatum + 3 Monate; Mietverhältnis und Mieteinheit werden »gekündigt«
  - `src/Service/AppointmentService.php::createHandover()` – Termin für den Auszug am Ende
- **Gegenbeobachtung:**
  - Die Frist steht fest im Controller, nicht in einem Service; sie ist für alle Mietverhältnisse gleich.
  - Ein Kündigungsdatum in der Zukunft oder weit in der Vergangenheit wird nicht geprüft.
- **Status:** zu klären
- **Offene Frage:** Gilt die Frist von drei Monaten für Kündigungen durch Mieter und durch Vermieter gleichermaßen? Gilt sie auch für Gewerbeeinheiten?

---

## REGEL-20

- **Regel:** Nur die Administration darf ein aktives oder gekündigtes Mietverhältnis fristlos aufheben.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `templates/tenancy/show.html.twig` – Button »Aufhebung / fristlos« nur bei aktivem oder gekündigtem Mietverhältnis und nur für `ROLE_ADMIN`
  - `src/Service/TenancyService.php::terminateImmediately()` – Mietverhältnis »aufgehoben«, Ende = Stichtag, Mieteinheit »gekündigt«, Termin für den Auszug
- **Gegenbeobachtung:** `src/Controller/TenancyController.php::terminateImmediately()` und der Service prüfen weder Rolle noch Zustand; `config/packages/security.yaml` erlaubt die Route jedem angemeldeten Benutzer. Damit lassen sich auch Entwürfe und beendete Mietverhältnisse aufheben.
- **Status:** widersprüchlich
- **Offene Frage:** Wer darf aufheben? Ist eine Aufhebung eines Entwurfs fachlich ein »Verwerfen«?

---

## REGEL-21

- **Regel:** Mit dem Auszug endet das Mietverhältnis; die Mieteinheit wird wieder frei oder, wenn Mängel festgestellt wurden, in Renovierung gesetzt.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/TenancyService.php::recordHandover()` – bei Auszug Mietverhältnis »beendet«, Ende = Auszugsdatum (falls leer), Mieteinheit »frei« oder »Renovierung«
  - `src/Controller/TenancyController.php::handover()` – schlägt bei gekündigtem oder aufgehobenem Mietverhältnis »Auszug« vor
- **Gegenbeobachtung:** Der Zustand des Mietverhältnisses wird nicht geprüft; ein Auszug beendet auch ein aktives (nicht gekündigtes) Mietverhältnis und über einen direkten Aufruf sogar einen Entwurf.
- **Status:** widersprüchlich
- **Offene Frage:** Ist ein Auszug ohne vorherige Kündigung oder Aufhebung zulässig (z. B. bei befristeten Mietverhältnissen)?

---

## REGEL-22

- **Regel:** Eine erledigte Aufgabe darf nur mit Begründung wiedereröffnet werden; die Begründung wird mit Datum in den Notizen festgehalten.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Controller/TaskController.php::reopen()` – verlangt bei erledigter Aufgabe eine Begründung, setzt »offen« und ergänzt die Notizen
  - `templates/task/edit.html.twig` – Button »Wieder eröffnen« nur bei nicht offener Aufgabe
- **Gegenbeobachtung:**
  - Eine abgebrochene Aufgabe kann ohne Begründung wiedereröffnet werden.
  - Über das Statusfeld in `src/Form/TaskType.php` kann jede Aufgabe ohne Begründung in jeden Status gesetzt werden.
  - `TaskController::done()` setzt »erledigt« ohne jede Prüfung, auch für abgebrochene Aufgaben.
- **Status:** widersprüchlich
- **Offene Frage:** Welche Statuswechsel einer Aufgabe sind fachlich erlaubt?

---

## REGEL-23

- **Regel:** Eine Aufgabe für einen Dienstleister braucht einen Ansprechpartner; ist ein Dienstleister eingetragen, gilt die Aufgabe als zugewiesen.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Service/TaskService.php::save()` – übernimmt den Ansprechpartner des Dienstleisters, lehnt ohne Ansprechpartner ab, setzt »offen« auf »zugewiesen«
  - `src/Service/IssueService.php::createTaskFromIssue()` – setzt bei vorgeschlagenem Dienstleister »zugewiesen«
- **Gegenbeobachtung:** `createTaskFromIssue()` prüft den Ansprechpartner nicht; hat der Dienstleister keinen, entsteht eine zugewiesene Aufgabe ohne Ansprechpartner.
- **Status:** widersprüchlich
- **Offene Frage:** Ist der Ansprechpartner Pflicht, oder genügt der Dienstleister?

---

## REGEL-24

- **Regel:** Aufgaben dürfen nur aktiven Dienstleistern zugeordnet werden; aus einer Meldung wird ein Dienstleister mit passendem Gewerk vorgeschlagen.
- **Bereich:** Meldungen/Aufgaben
- **Fundstellen:**
  - `src/Form/TaskType.php` – Auswahl nur aus aktiven Dienstleistern (`ServiceProviderRepository::createActiveQueryBuilder()`)
  - `src/Service/IssueService.php::createTaskFromIssue()` – Vorschlag nach Kategorie der Meldung (Heizung, Sanitär, Elektro, Aufzug)
- **Gegenbeobachtung:** Der Vorschlag in `createTaskFromIssue()` sucht nur nach dem Gewerk und berücksichtigt den Status nicht; ein inaktiver Dienstleister kann vorgeschlagen werden. Bei mehreren passenden wird der erste genommen.
- **Status:** widersprüchlich
- **Offene Frage:** Nach welchen Kriterien soll unter mehreren passenden Dienstleistern ausgewählt werden?

---

## REGEL-25

- **Regel:** Nach einer Zusage wird der Mietinteressent als Mieter übernommen; ist die gewünschte Mieteinheit frei, wird sie für ihn reserviert.
- **Bereich:** Vermietung
- **Fundstellen:**
  - `src/Service/RentalService.php::acceptProspect()` – legt den Mieter an, Interessentenstatus »Zusage«, reserviert nur eine freie Mieteinheit
  - `templates/letting/prospects.html.twig` – Button »Zusage« nur bei Status neu, Besichtigung geplant oder Bewerbung eingegangen
  - `src/Controller/LettingController.php::accept()` – leitet zur Anlage des Mietverhältnisses weiter
- **Gegenbeobachtung:**
  - Der Service prüft den Interessentenstatus nicht; eine zweite Zusage legt den Mieter ein zweites Mal an.
  - Der Name wird am letzten Leerzeichen in Vor- und Nachname getrennt; bei einem einzigen Wort ist der Vorname »-«.
  - Die Reservierung hat kein Ablaufdatum und wird nicht aufgehoben, wenn kein Mietverhältnis folgt.
- **Status:** widersprüchlich
- **Offene Frage:** Wie lange gilt eine Reservierung, und wer hebt sie wieder auf?

---

## REGEL-26

- **Regel:** Jede Liegenschaft gehört genau einem Eigentümer.
- **Bereich:** Bestand
- **Fundstellen:**
  - `src/Entity/Property.php::owner` – Pflichtverweis auf einen Eigentümer
  - `src/Form/PropertyType.php` – Auswahl des Eigentümers
- **Gegenbeobachtung:**
  - Auch inaktive Eigentümer sind auswählbar; der Eigentümerstatus wird nirgends ausgewertet.
  - Ein Eigentümerwechsel ist durch Bearbeiten jederzeit möglich, ohne Stichtag; nur Buchungen merken sich den Eigentümer zum Buchungszeitpunkt (`src/Controller/AccountingController.php::newBooking()`).
- **Status:** widersprüchlich
- **Offene Frage:** Kann eine Liegenschaft mehreren Eigentümern gehören (Eigentümergemeinschaft)? Wie wird ein Eigentümerwechsel zu einem Stichtag abgebildet?

---

## REGEL-27

- **Regel:** Liegenschaften, Gebäude und Eigentümer erhalten beim Anlegen eine Nummer: Objektnummer `OB-0xxx`, Gebäudenummer aus Objektnummer und laufender Gebäudezahl (`OB-0xxx-G1`), Eigentümernummer `EI-xxxx`.
- **Bereich:** Bestand
- **Fundstellen:**
  - `src/Service/PropertyService.php::nextPropertyNumber()` – Objektnummer = Anzahl Liegenschaften + 101
  - `src/Controller/PropertyController.php::newBuilding()` – Gebäudenummer
  - `src/Controller/OwnerController.php::new()` – Eigentümernummer = Anzahl Eigentümer + 1
  - `src/Entity/Property.php::propertyNumber`, `src/Entity/Owner.php::ownerNumber` – eindeutig
- **Gegenbeobachtung:**
  - Die Nummern sind nur Vorschläge und im Formular änderbar; eine geänderte Nummer kann mit einem späteren Vorschlag kollidieren.
  - Die Gebäudenummer ist nicht eindeutig.
- **Status:** widersprüchlich
- **Offene Frage:** Sind die Nummern frei vergebbar oder fest vom System vergeben?

---

## REGEL-28

- **Regel:** Ein Gebäude gehört zu genau einer Liegenschaft und übernimmt beim Anlegen deren Anschrift; eine Mieteinheit gehört zu genau einem Gebäude.
- **Bereich:** Bestand
- **Fundstellen:**
  - `src/Entity/Building.php::property`, `src/Entity/RentalUnit.php::building` – Pflichtverweise
  - `src/Controller/PropertyController.php::newBuilding()` – übernimmt Straße und Hausnummer der Liegenschaft
  - `src/Controller/RentalUnitController.php::new()` – Mieteinheit wird für ein Gebäude angelegt
- **Gegenbeobachtung:**
  - Die Einheitsnummer ist weder je Gebäude noch insgesamt eindeutig.
  - Über `src/Form/RentalUnitType.php` kann eine Mieteinheit einem anderen Gebäude, auch einer anderen Liegenschaft, zugeordnet werden – auch während sie vermietet ist.
- **Status:** widersprüchlich
- **Offene Frage:** Woran wird eine Mieteinheit fachlich eindeutig erkannt? Darf sie das Gebäude wechseln?

---

## REGEL-29

- **Regel:** Der Einheitenstatus einer Mieteinheit folgt aus der Vermietung: reserviert bei Entwurf oder Zusage, vermietet bei aktivem, gekündigt bei gekündigtem oder aufgehobenem Mietverhältnis, frei oder Renovierung nach dem Auszug.
- **Bereich:** Bestand
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()`, `activate()`, `terminateImmediately()`, `recordHandover()`
  - `src/Controller/TenancyController.php::terminate()`
  - `src/Service/RentalService.php::acceptProspect()`
- **Gegenbeobachtung:**
  - In `src/Form/RentalUnitType.php` ist der Einheitenstatus frei wählbar; eine vermietete Mieteinheit kann ohne Mietverhältnis auf »frei« gesetzt werden und umgekehrt.
  - »Renovierung → frei« und »gesperrt« sind nur über dieses Formular erreichbar.
- **Status:** widersprüchlich
- **Offene Frage:** Welche Einheitenstatus darf der Verwalter direkt setzen (z. B. nur »gesperrt« und »Renovierung beendet«)?

---

## REGEL-30

- **Regel:** Ein Mieter hat einen Vor- und einen Nachnamen.
- **Bereich:** Bestand
- **Fundstellen:**
  - `src/Entity/Tenant.php::firstName`, `lastName` – Pflichtfelder
  - `src/Form/TenantType.php` – Erfassung
- **Gegenbeobachtung:** `src/Service/RentalService.php::acceptProspect()` setzt bei einem einteiligen Namen den Vornamen »-«; der Mietinteressent hat nur ein Namensfeld.
- **Status:** widersprüchlich
- **Offene Frage:** Wie werden Firmen als Mieter erfasst (siehe REGEL-14)?
