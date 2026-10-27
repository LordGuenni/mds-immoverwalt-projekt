# Regelkatalog ImmoVerwalt

Fachliche Regeln der Pflichtbereiche, rekonstruiert aus dem Code (Ü2) und präzisiert nach
Leitfaden Schritt 7. Begriffe nach [glossar.md](./glossar.md), Zustände und Übergänge nach
[verhalten.md](./verhalten.md) (Befunde B-xx), Objekte nach [fachmodell.md](./fachmodell.md).
Entscheidungen sind im [Logbuch](./logbuch.md) begründet, offene Fragen F-xx stehen dort.

**Regeln formulieren:** als deutscher Satz, ohne technische Begriffe (kein Controller, kein Template,
keine Tabelle). Code-Namen stehen nur unter *Fundstellen* und *Gegenbeobachtung*.

**Status** (beschreibt das Altsystem): `bestätigt` · `widersprüchlich` · `zu klären`

| Feld | Bedeutung |
|---|---|
| Regel | fachliche Aussage in einem Satz |
| Bereich | Bestand · Vermietung · Meldungen/Aufgaben |
| Anwendungsfall | Anwendungsfälle (AF-xx), bei denen die Regel geprüft wird |
| Vorbedingung | gilt vor der Aktion; sonst wird die Aktion abgelehnt |
| Nachbedingung | gilt nach der Aktion |
| Invariante | gilt immer, nach jeder Aktion |
| Betroffene Objekte | Entities, die gleichzeitig gelesen oder geändert werden (Grundlage für Schritt 8) |
| Aggregat | Aggregat, das den Zustand der Regel hält (fachmodell.md, Abschnitt Aggregate); „+ Anwendungsschicht“: die Bedingung liest weitere Aggregate |
| Fundstellen | Datei und Methode bzw. Template, an denen die Regel umgesetzt ist |
| Gegenbeobachtung | Stellen, an denen die Regel fehlt, umgangen wird oder anders umgesetzt ist |
| Status | siehe oben |
| Entscheidung | wie die Regel im Modell gilt, besonders bei Widersprüchen im Altsystem |
| Offene Frage | Verweis auf die Liste im Logbuch |

Eine Regel hat mindestens eine Vor- oder Nachbedingung oder Invariante. Felder ohne Inhalt fehlen.

## Übersicht

| Regel | Titel | Bereich | Art | Aggregat | Status |
|---|---|---|---|---|---|
| REGEL-01 | Mietverhältnis nur für freie oder reservierte Mieteinheit | Vermietung | Vorbedingung | Mietverhältnis + Anwendungsschicht | widersprüchlich |
| REGEL-02 | Meldung nur ohne offene Aufgaben schließen | Meldungen/Aufgaben | Vorbedingung, Invariante | Meldung + Anwendungsschicht | widersprüchlich |
| REGEL-03 | Meldung gehört zur Liegenschaft ihrer Mieteinheit | Meldungen/Aufgaben | Invariante, Nachbedingung | Meldung + Anwendungsschicht | widersprüchlich |
| REGEL-04 | Bearbeiter einer neuen Meldung | Meldungen/Aufgaben | Nachbedingung | Meldung | bestätigt |
| REGEL-05 | Dringende Meldung sofort in Prüfung | Meldungen/Aufgaben | Nachbedingung | Meldung | zu klären |
| REGEL-06 | *zusammengeführt mit REGEL-02* | – | – | – | – |
| REGEL-07 | Neue Aufgabe setzt Meldung in Bearbeitung | Meldungen/Aufgaben | Nachbedingung | Meldung (Reaktion auf Ereignis der Aufgabe) | widersprüchlich |
| REGEL-08 | Priorität und Fälligkeit einer Aufgabe aus einer Meldung | Meldungen/Aufgaben | Nachbedingung | Aufgabe + Anwendungsschicht | bestätigt |
| REGEL-09 | Statushistorie der Meldung | Meldungen/Aufgaben | Invariante | Meldung | widersprüchlich |
| REGEL-10 | Meldungs- und Aufgabennummer | Meldungen/Aufgaben | Invariante, Nachbedingung | Meldung bzw. Aufgabe (Nummernvergabe) | bestätigt |
| REGEL-11 | Aufgabe braucht einen Titel | Meldungen/Aufgaben | Invariante | Aufgabe | bestätigt |
| REGEL-12 | Keine überschneidenden Laufzeiten | Vermietung | Invariante, Vorbedingung | Mietverhältnis + Anwendungsschicht | widersprüchlich |
| REGEL-13 | Ende nicht vor Beginn | Vermietung | Invariante | Mietverhältnis | widersprüchlich |
| REGEL-14 | Mietverhältnis braucht mindestens einen Mieter | Vermietung | Vorbedingung, Invariante | Mietverhältnis | widersprüchlich |
| REGEL-15 | Kündigung ergibt sich aus Zustand und Kündigungsdatum | Vermietung | Invariante | Mietverhältnis | widersprüchlich |
| REGEL-16 | Konditionen beim Anlegen festlegen | Vermietung | Nachbedingung | Mietverhältnis + Anwendungsschicht | bestätigt |
| REGEL-17 | Übergaben protokollieren | Vermietung | Vorbedingung, Invariante | Mietverhältnis | bestätigt |
| REGEL-18 | Vertragsnummer | Vermietung | Invariante, Nachbedingung | Mietverhältnis (Nummernvergabe) | bestätigt |
| REGEL-19 | Kündigung eines Mietverhältnisses | Vermietung | Vor- und Nachbedingung | Mietverhältnis (Folgen über Ereignisse) | zu klären |
| REGEL-20 | Aufhebung eines Mietverhältnisses | Vermietung | Vor- und Nachbedingung | Mietverhältnis + Anwendungsschicht (Rolle) | widersprüchlich |
| REGEL-21 | Auszug beendet das Mietverhältnis | Vermietung | Vor- und Nachbedingung | Mietverhältnis (Folgen über Ereignisse) | widersprüchlich |
| REGEL-22 | Aufgabe nur mit Begründung wiedereröffnen | Meldungen/Aufgaben | Vor- und Nachbedingung | Aufgabe + Anwendungsschicht | widersprüchlich |
| REGEL-23 | Ansprechpartner bei Dienstleister-Aufgaben | Meldungen/Aufgaben | Invariante, Nachbedingung | Aufgabe + Anwendungsschicht | widersprüchlich |
| REGEL-24 | Nur aktive Dienstleister, Vorschlag nach Gewerk | Meldungen/Aufgaben | Vor- und Nachbedingung | Aufgabe + Anwendungsschicht | widersprüchlich |
| REGEL-25 | Zusage an einen Mietinteressenten | Vermietung | Vor- und Nachbedingung | Mietinteressent + Anwendungsschicht | widersprüchlich |
| REGEL-26 | Liegenschaft gehört genau einem Eigentümer | Bestand | Invariante, Vorbedingung | Liegenschaft + Anwendungsschicht | widersprüchlich |
| REGEL-27 | Objekt-, Gebäude- und Eigentümernummer | Bestand | Invariante, Nachbedingung | Liegenschaft bzw. Eigentümer (Nummernvergabe) | widersprüchlich |
| REGEL-28 | Hierarchie Liegenschaft – Gebäude – Mieteinheit | Bestand | Invariante, Nachbedingung | Liegenschaft (Gebäude) bzw. Mieteinheit + Anwendungsschicht | widersprüchlich |
| REGEL-29 | Einheitenstatus folgt der Vermietung | Bestand | Invariante | Mieteinheit (Reaktion auf Ereignisse) | widersprüchlich |
| REGEL-30 | Mieter hat Vor- und Nachnamen | Bestand | Invariante | Mieter | widersprüchlich |
| REGEL-31 | Entwurf verwerfen und Reservierung aufheben | Vermietung | Vor- und Nachbedingung | Mietverhältnis (verwerfen) bzw. Mieteinheit (Reservierung aufheben) + Anwendungsschicht | neu |
| REGEL-32 | Meldung nur mit Kommentar ablehnen | Meldungen/Aufgaben | Vor- und Nachbedingung | Meldung + Anwendungsschicht | neu |
| REGEL-33 | Aufgabe nur mit Begründung abbrechen | Meldungen/Aufgaben | Vor- und Nachbedingung | Aufgabe | neu |
| REGEL-34 | Zustände ändern sich nur über erlaubte Übergänge | alle | Invariante | jedes Aggregat für seinen eigenen Status | neu |
| REGEL-35 | Verwaltung einer Liegenschaft beenden | Bestand | Vorbedingung | Liegenschaft + Anwendungsschicht | neu |

Status `neu`: Die Regel fehlt im Altsystem und wurde in Schritt 6 aus einem Befund abgeleitet.

---

## REGEL-01 Mietverhältnis nur für freie oder reservierte Mieteinheit

- **Regel:** Ein Mietverhältnis darf nur für eine Mieteinheit angelegt werden, die frei oder reserviert ist.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-04 Mietverhältnis als Entwurf anlegen
- **Vorbedingung:** Einheitenstatus der Mieteinheit ist *frei* oder *reserviert*.
- **Nachbedingung:** Ein Mietverhältnis im Zustand *Entwurf* besteht; die Mieteinheit ist *reserviert* (REGEL-29).
- **Betroffene Objekte:** Mieteinheit, Mietverhältnis
- **Aggregat:** Mietverhältnis + Anwendungsschicht
- **Fundstellen:**
  - `templates/unit/show.html.twig` – Button »Mietvertrag anlegen« nur bei Status `AVAILABLE`
  - `src/Controller/TenancyController.php::new()` – prüft den Status der Einheit
  - `src/Service/TenancyService.php::createTenancy()` – prüft vorhandene aktive Mietverhältnisse
  - `src/Repository/TenancyRepository.php::findActiveForUnit()` – Abfrage der aktiven Verträge
- **Gegenbeobachtung:** Der Controller lässt auch reservierte Einheiten zu, das Template bietet den Vertrag nur bei freien Einheiten an.
- **Status:** widersprüchlich
- **Entscheidung:** *Reserviert* ist zugelassen, weil die Zusage (REGEL-25) die Mieteinheit reserviert und das Mietverhältnis danach angelegt wird (B-10). Die zweite Prüfung „kein aktives Mietverhältnis“ entfällt als eigene Bedingung: Sie ist durch den Einheitenstatus (REGEL-29) und die Überschneidungsregel (REGEL-12) abgedeckt.
- **Offene Frage:** F-01, F-23

---

## REGEL-02 Meldung nur ohne offene Aufgaben schließen

- **Regel:** Eine Meldung darf nur geschlossen oder erledigt werden, wenn alle ihre Aufgaben erledigt oder abgebrochen sind.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-19 Meldung schließen, AF-17 Meldungsstatus ändern (erledigen)
- **Vorbedingung:** Keine Aufgabe der Meldung ist im Aufgabenstatus *offen*, *zugewiesen*, *in Bearbeitung* oder *wartet*.
- **Nachbedingung:** Meldungsstatus *geschlossen* bzw. *erledigt*; ein Statuswechsel ist eingetragen (REGEL-09).
- **Invariante:** Eine geschlossene Meldung hat keine offene Aufgabe. Daraus folgt: Aufgaben einer geschlossenen Meldung können nicht wiedereröffnet werden (REGEL-22), und einer geschlossenen Meldung kann keine Aufgabe hinzugefügt werden.
- **Betroffene Objekte:** Meldung, Aufgabe
- **Aggregat:** Meldung + Anwendungsschicht
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
- **Entscheidung:** Die Regel gilt ausnahmslos und auf jedem Weg, auch bei der Sammeländerung (B-15, B-19). Eine Ausnahme für die Administration wird nicht modelliert, solange F-02 offen ist.
- **Offene Frage:** F-02, F-22

---

## REGEL-03 Meldung gehört zur Liegenschaft ihrer Mieteinheit

- **Regel:** Eine Meldung zu einer Mieteinheit gehört immer zur Liegenschaft dieser Mieteinheit; ist kein meldender Mieter angegeben und hat das laufende Mietverhältnis genau einen Mieter, wird dieser eingetragen.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-16 Meldung erfassen
- **Invariante:** Ist eine Mieteinheit angegeben, ist die Liegenschaft der Meldung die Liegenschaft des Gebäudes dieser Mieteinheit – beim Erfassen und bei jeder Änderung.
- **Nachbedingung (erfassen):** Ist kein meldender Mieter angegeben, eine Mieteinheit aber schon, und hat das aktive oder gekündigte Mietverhältnis dieser Mieteinheit genau einen Mieter, dann ist dieser der meldende Mieter. Sonst bleibt der meldende Mieter leer.
- **Betroffene Objekte:** Meldung; gelesen: Mieteinheit, Gebäude, Liegenschaft, Mietverhältnis, Mieter
- **Aggregat:** Meldung + Anwendungsschicht
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt die Liegenschaft aus der Mieteinheit und ergänzt den Mieter
  - `src/Controller/IssueController.php::new()` – übernimmt Mieteinheit und Liegenschaft aus dem Aufruf
  - `src/Repository/TenancyRepository.php::findActiveForUnit()` – liefert aktive und gekündigte Mietverhältnisse
- **Gegenbeobachtung:**
  - Eingetragen wird der erste Mieter des ersten gefundenen Mietverhältnisses; bei mehreren Mietern ist die Auswahl zufällig.
  - Im Formular gewählte Liegenschaft wird beim Erfassen stillschweigend überschrieben; beim Bearbeiten (`IssueController::edit()`) dagegen nicht, dort können Liegenschaft und Mieteinheit auseinanderlaufen.
- **Status:** widersprüchlich
- **Entscheidung:** Die Zuordnung zur Liegenschaft ist eine Invariante und gilt auch beim Bearbeiten. Bei mehreren Mietern wird niemand automatisch eingetragen, weil eine zufällige Auswahl fachlich falsch sein kann.
- **Offene Frage:** F-03

---

## REGEL-04 Bearbeiter einer neuen Meldung

- **Regel:** Eine neue Meldung ohne Bearbeiter erhält den erfassenden Mitarbeiter als Bearbeiter.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-16 Meldung erfassen
- **Nachbedingung:** Ist kein Bearbeiter angegeben, ist der erfassende Benutzer Bearbeiter der Meldung.
- **Betroffene Objekte:** Meldung
- **Aggregat:** Meldung
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt den Bearbeiter auf den erfassenden Benutzer
  - `src/Controller/IssueController.php::new()` – übergibt den angemeldeten Benutzer
- **Gegenbeobachtung:** keine; über `IssueController::edit()` kann jeder angemeldete Benutzer den Bearbeiter ändern.
- **Status:** bestätigt
- **Entscheidung:** übernommen wie im Altsystem.
- **Offene Frage:** Wer darf einer Meldung einen anderen Bearbeiter zuweisen? (F-24)

---

## REGEL-05 Dringende Meldung sofort in Prüfung

- **Regel:** Eine dringende Meldung (Priorität »dringend«) wird bei der Erfassung sofort in Prüfung genommen.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-16 Meldung erfassen
- **Nachbedingung:** Priorität *dringend* → Meldungsstatus *in Prüfung*; sonst *neu*. Der erste Statuswechsel (von leer nach diesem Status) ist eingetragen (REGEL-09).
- **Betroffene Objekte:** Meldung
- **Aggregat:** Meldung
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt bei Priorität 4 den Meldungsstatus `IN_REVIEW`
  - `src/Entity/Issue.php::PRIORITIES` – Priorität 4 heißt »dringend«
- **Gegenbeobachtung:**
  - Wird die Priorität später über `IssueController::edit()` auf »dringend« erhöht, ändert sich der Status nicht.
  - Für die Fälligkeit von Aufgaben gilt eine andere Schwelle (ab »hoch«, siehe REGEL-08).
- **Status:** zu klären
- **Entscheidung:** vorläufig wie im Altsystem: nur beim Erfassen und nur für *dringend*. Eine spätere Erhöhung ändert den Meldungsstatus nicht. Die unterschiedliche Schwelle zu REGEL-08 bleibt bestehen, bis F-04 beantwortet ist.
- **Offene Frage:** F-04

---

## REGEL-06

- **Zusammengeführt mit REGEL-02** (beschrieb dieselbe Regel; siehe Logbuch). Die Nummer bleibt frei, damit Verweise eindeutig bleiben.

---

## REGEL-07 Neue Aufgabe setzt Meldung in Bearbeitung

- **Regel:** Wird zu einer neuen, in Prüfung befindlichen oder erledigten Meldung eine Aufgabe angelegt oder wiedereröffnet, ist die Meldung danach in Bearbeitung.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-18 Aufgabe zu einer Meldung anlegen, AF-21 Aufgabe anlegen, AF-23 Aufgabe wiedereröffnen
- **Vorbedingung:** Die Meldung ist nicht *geschlossen* und nicht *abgelehnt* (REGEL-02, REGEL-32).
- **Nachbedingung:** War die Meldung *neu*, *in Prüfung* oder *erledigt*, ist sie *in Bearbeitung*; ein Statuswechsel ist eingetragen (REGEL-09). War sie bereits *in Bearbeitung*, bleibt sie es.
- **Betroffene Objekte:** Aufgabe, Meldung
- **Aggregat:** Meldung (Reaktion auf Ereignis der Aufgabe)
- **Fundstellen:**
  - `src/Service/IssueService.php::createTaskFromIssue()` – setzt die Meldung von `NEW` oder `IN_REVIEW` auf `IN_PROGRESS`
  - `src/Controller/IssueController.php::addTask()` – legt die Aufgabe aus der Meldung an
- **Gegenbeobachtung:**
  - Eine Aufgabe, die über »Neue Aufgabe« (`TaskController::new()` → `TaskService::save()`) mit Bezug zur Meldung angelegt wird, ändert den Meldungsstatus nicht.
  - Der Statuswechsel wird nicht in der Statushistorie festgehalten (siehe REGEL-09).
- **Status:** widersprüchlich
- **Entscheidung:** Die Regel gilt unabhängig vom Weg, auf dem die Aufgabe entsteht (B-18), und auch beim Wiedereröffnen (B-19).
- **Offene Frage:** –

---

## REGEL-08 Priorität und Fälligkeit einer Aufgabe aus einer Meldung

- **Regel:** Eine Aufgabe zu einer Meldung übernimmt deren Priorität und ist bei Priorität »hoch« oder »dringend« in 2 Tagen, sonst in 14 Tagen fällig.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-18 Aufgabe zu einer Meldung anlegen, AF-21 Aufgabe anlegen (mit Meldung)
- **Nachbedingung:** Sind Priorität bzw. Fälligkeit nicht angegeben: Priorität der Aufgabe = Priorität der Meldung; Fälligkeit = Tag der Anlage + 2 Tage bei *hoch* oder *dringend*, sonst + 14 Tage.
- **Betroffene Objekte:** Aufgabe; gelesen: Meldung
- **Aggregat:** Aufgabe + Anwendungsschicht
- **Fundstellen:**
  - `src/Service/IssueService.php::createTaskFromIssue()` – übernimmt die Priorität und setzt die Fälligkeit (`+2 days` ab Priorität 3, sonst `+14 days`)
  - `src/Entity/Issue.php::PRIORITIES` – Prioritätsstufen
- **Gegenbeobachtung:**
  - Über »Neue Aufgabe« (`TaskController::new()`) gibt es weder übernommene Priorität noch Fälligkeit.
  - Ändert sich die Priorität der Meldung später, bleibt die Fälligkeit der Aufgabe unverändert.
- **Status:** bestätigt
- **Entscheidung:** gilt für jede Aufgabe mit Meldung, unabhängig vom Weg; angegebene Werte haben Vorrang. Spätere Änderungen der Meldung wirken nicht auf bestehende Aufgaben.
- **Offene Frage:** Sind 2 bzw. 14 Tage verbindliche Fristen des Fachbereichs? (F-04)

---

## REGEL-09 Statushistorie der Meldung

- **Regel:** Jeder Statuswechsel einer Meldung wird mit altem und neuem Status, Zeitpunkt, Benutzer und Kommentar in der Statushistorie festgehalten.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-16 bis AF-19 und jeder Übergang des Meldungsstatus in verhalten.md
- **Invariante:** Für jeden Übergang des Meldungsstatus gibt es genau einen Statuswechsel mit: von (beim Erfassen leer), nach, Zeitpunkt, geändert von, Kommentar (optional, beim Ablehnen Pflicht – REGEL-32). Die Statushistorie wird nur ergänzt, nie geändert. Der letzte Eintrag entspricht dem aktuellen Meldungsstatus.
- **Betroffene Objekte:** Meldung (mit Statushistorie)
- **Aggregat:** Meldung
- **Fundstellen:**
  - `src/Controller/IssueController.php::status()` – schreibt bei jedem manuellen Wechsel einen Eintrag
  - `src/Service/IssueService.php::createIssue()` – schreibt den ersten Eintrag beim Erfassen
  - `src/Entity/IssueStatusChange.php` – Eintrag der Statushistorie
- **Gegenbeobachtung:** Drei Wege ändern den Status **ohne** Eintrag in der Statushistorie:
  - `src/Service/IssueService.php::closeIssue()` (Schließen)
  - `src/Service/IssueService.php::createTaskFromIssue()` (in Bearbeitung durch neue Aufgabe)
  - `src/Controller/IssueController.php::bulk()` (Sammeländerung)
- **Status:** widersprüchlich
- **Entscheidung:** gilt ohne Ausnahme (B-16). Weil die Statushistorie Teil der Meldung ist, kann der Statuswechsel nicht vergessen werden, wenn jede Änderung über die Meldung läuft (Schritt 8).
- **Offene Frage:** F-05

---

## REGEL-10 Meldungs- und Aufgabennummer

- **Regel:** Jede Meldung und jede Aufgabe erhält beim Anlegen eine eindeutige, fortlaufende Nummer.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-16 Meldung erfassen, AF-18 und AF-21 Aufgabe anlegen
- **Invariante:** Meldungsnummern sind untereinander eindeutig, Aufgabennummern ebenso; eine vergebene Nummer ändert sich nie.
- **Nachbedingung:** Neue Meldung: Meldungsnummer = `ME-` und die nächste Zahl der Folge (ab 02001). Neue Aufgabe: Aufgabennummer = `AU-` und die nächste Zahl der Folge (ab 03001).
- **Betroffene Objekte:** Meldung bzw. Aufgabe
- **Aggregat:** Meldung bzw. Aufgabe (Nummernvergabe)
- **Fundstellen:**
  - `src/Service/IssueService.php::createIssue()` – setzt die Nummer
  - `src/Repository/IssueRepository.php::nextNumber()` – Nummer = `ME-` + (2000 + Anzahl Meldungen + 1)
  - `src/Entity/Issue.php::issueNumber` – eindeutig
- **Gegenbeobachtung:** Die Nummer wird aus der Anzahl der Meldungen berechnet; werden zwei Meldungen gleichzeitig erfasst, erhalten beide dieselbe Nummer und die zweite scheitert an der Eindeutigkeit. Aufgaben (`TaskRepository::nextNumber()`, `AU-`) werden genauso nummeriert.
- **Status:** bestätigt
- **Entscheidung:** „fortlaufend“ heißt: aus einer Folge, die nie eine Nummer zweimal liefert – nicht aus der Anzahl der vorhandenen Meldungen.
- **Offene Frage:** Ist die Meldungsnummer nach außen sichtbar (z. B. auf der Mitteilung an den Mieter)? (F-25)

---

## REGEL-11 Aufgabe braucht einen Titel

- **Regel:** Jede Aufgabe braucht einen Titel.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-18, AF-21
- **Invariante:** Der Titel einer Aufgabe ist nicht leer.
- **Betroffene Objekte:** Aufgabe
- **Aggregat:** Aufgabe
- **Fundstellen:**
  - `src/Controller/IssueController.php::addTask()` – lehnt einen leeren Titel ab
  - `src/Entity/Task.php::title` – Pflichtfeld im Formular »Neue Aufgabe«
- **Gegenbeobachtung:** keine
- **Status:** bestätigt
- **Entscheidung:** übernommen wie im Altsystem.

---

## REGEL-12 Keine überschneidenden Laufzeiten

- **Regel:** Die Laufzeiten der Mietverhältnisse einer Mieteinheit dürfen sich nicht überschneiden.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-04 Mietverhältnis als Entwurf anlegen, AF-06 Mietverhältnis aktivieren
- **Invariante:** Für jede Mieteinheit überschneiden sich die Laufzeiten ihrer Mietverhältnisse im Zustand *aktiv*, *gekündigt*, *aufgehoben* oder *beendet* nicht. Eine Laufzeit ohne Ende reicht bis unendlich.
- **Vorbedingung:** Anlegen und Aktivieren: Die Laufzeit des Mietverhältnisses überschneidet sich mit keiner dieser Laufzeiten.
- **Betroffene Objekte:** Mietverhältnis; gelesen: alle Mietverhältnisse derselben Mieteinheit
- **Aggregat:** Mietverhältnis + Anwendungsschicht
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – lehnt überschneidende Laufzeiten ab
  - `src/Repository/TenancyRepository.php::findOverlapping()` – sucht Mietverhältnisse derselben Mieteinheit mit überschneidender Laufzeit
- **Gegenbeobachtung:**
  - `findOverlapping()` ignoriert Entwürfe: Für eine Mieteinheit können mehrere Entwürfe mit gleicher Laufzeit angelegt werden.
  - `TenancyService::activate()` prüft die Überschneidung nicht erneut; zwei überlappende Entwürfe lassen sich nacheinander aktivieren.
  - Unterschied zu REGEL-01: REGEL-01 betrifft den Einheitenstatus bei der Anlage, REGEL-12 die zeitliche Überschneidung.
- **Status:** widersprüchlich
- **Entscheidung:** Entwürfe dürfen sich überschneiden, solange F-06 offen ist. Damit die Invariante trotzdem gilt, wird beim **Aktivieren** erneut geprüft. Weil die Regel mehrere Mietverhältnisse betrifft, prüft sie die Anwendungsschicht (Schritt 8).
- **Offene Frage:** F-06

---

## REGEL-13 Ende nicht vor Beginn

- **Regel:** Das Ende eines Mietverhältnisses liegt nicht vor seinem Beginn; ohne Ende ist es unbefristet.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-04, AF-07, AF-08, AF-09
- **Invariante:** Laufzeit: Ende ist leer oder Ende ≥ Beginn – auch nach Kündigung, Aufhebung und Auszug.
- **Betroffene Objekte:** Mietverhältnis
- **Aggregat:** Mietverhältnis
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – lehnt ein Ende vor dem Beginn ab
  - `templates/tenancy/show.html.twig` – zeigt ohne Ende »unbefristet«
- **Gegenbeobachtung:** Bei Kündigung (`TenancyController::terminate()`) und Aufhebung (`TenancyService::terminateImmediately()`) wird das Ende ohne Prüfung gesetzt; ein Stichtag vor dem Beginn wird angenommen.
- **Status:** widersprüchlich
- **Entscheidung:** Die Invariante gilt für jede Änderung der Laufzeit. Eine Aufhebung mit Stichtag vor dem Beginn wird abgelehnt; frühester Stichtag ist der Beginn (REGEL-20). Die Prüfung gehört zum Wertobjekt Zeitraum.
- **Offene Frage:** –

---

## REGEL-14 Mietverhältnis braucht mindestens einen Mieter

- **Regel:** Ein Mietverhältnis kann nur mit mindestens einem Mieter aktiviert werden, und danach darf ihm der letzte Mieter nicht entfernt werden.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-05 Mieter eines Mietverhältnisses ändern, AF-06 Mietverhältnis aktivieren
- **Vorbedingung:** Aktivieren: mindestens ein Mieter ist eingetragen.
- **Invariante:** Ein Mietverhältnis im Zustand *aktiv*, *gekündigt*, *aufgehoben* oder *beendet* hat mindestens einen Mieter. Ein beendetes Mietverhältnis ändert seine Mieter nicht mehr.
- **Betroffene Objekte:** Mietverhältnis; gelesen: Mieter
- **Aggregat:** Mietverhältnis
- **Fundstellen:**
  - `src/Service/TenancyService.php::activate()` – verlangt mindestens einen Mieter
  - `src/Service/TenancyService.php::removeTenant()` – verhindert das Entfernen des letzten Mieters bei aktivem Mietverhältnis
- **Gegenbeobachtung:** `removeTenant()` prüft nur den Zustand »aktiv«; bei einem gekündigten Mietverhältnis kann der letzte Mieter entfernt werden.
- **Status:** widersprüchlich
- **Entscheidung:** Die Invariante gilt in allen Zuständen nach dem Entwurf. Auch ein gekündigtes Mietverhältnis hat bis zum Auszug einen Vertragspartner.
- **Offene Frage:** F-07

---

## REGEL-15 Kündigung ergibt sich aus Zustand und Kündigungsdatum

- **Regel:** Ob ein Mietverhältnis gekündigt ist, ergibt sich aus seinem Zustand und dem Kündigungsdatum.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-07 Mietverhältnis kündigen
- **Invariante:** Das Kündigungsdatum ist genau dann gesetzt, wenn das Mietverhältnis gekündigt wurde – also im Zustand *gekündigt* und in allen Zuständen, die aus *gekündigt* erreicht wurden. Ein weiteres Kennzeichen „gekündigt“ gibt es nicht.
- **Betroffene Objekte:** Mietverhältnis
- **Aggregat:** Mietverhältnis
- **Fundstellen:**
  - `src/Entity/Tenancy.php::status` – Zustand `NOTICE_GIVEN` (gekündigt)
  - `src/Entity/Tenancy.php::noticeDate` – Kündigungsdatum
  - `src/Entity/Tenancy.php::noticeGiven` – zusätzliches Kennzeichen (Ticket #213)
- **Gegenbeobachtung:**
  - Die Kündigung ist doppelt abgelegt: als Zustand und als Kennzeichen `noticeGiven`.
  - Die ordentliche Kündigung (`TenancyController::terminate()`) setzt das Kennzeichen **nicht**; nur die Aufhebung (`TenancyService::terminateImmediately()`) setzt es.
  - `templates/tenancy/show.html.twig` blendet »Kündigung erfassen« nur aus, wenn das Kennzeichen gesetzt ist; der Controller selbst prüft nur den Zustand.
- **Status:** widersprüchlich
- **Entscheidung:** Das Kennzeichen wird nicht ins Modell übernommen; maßgeblich sind Zustand und Kündigungsdatum (B-06, Logbuch).
- **Offene Frage:** F-08

---

## REGEL-16 Konditionen beim Anlegen festlegen

- **Regel:** Kaltmiete, Nebenkostenvorauszahlung und Kaution werden beim Anlegen des Mietverhältnisses festgelegt; fehlen sie, gelten Angebotsmiete und Nebenkostenvorauszahlung der Mieteinheit und eine Kaution von drei Kaltmieten.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-04 Mietverhältnis als Entwurf anlegen
- **Nachbedingung:** Kaltmiete = angegebener Betrag, sonst Angebotsmiete der Mieteinheit; Nebenkostenvorauszahlung = angegebener Betrag, sonst die der Mieteinheit; Kaution = angegebener Betrag, sonst 3 × Kaltmiete. Alle drei sind gesetzt.
- **Invariante:** Eine spätere Änderung der Angebotsmiete ändert die Kaltmiete bestehender Mietverhältnisse nicht.
- **Betroffene Objekte:** Mietverhältnis; gelesen: Mieteinheit
- **Aggregat:** Mietverhältnis + Anwendungsschicht
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()` – übernimmt fehlende Werte aus der Mieteinheit, Kaution = 3 × Kaltmiete
  - `src/Controller/TenancyController.php::new()` – füllt das Formular mit den Werten der Mieteinheit vor
  - `src/Entity/Tenancy.php::coldRent`, `serviceChargeAdvance`, `deposit`
- **Gegenbeobachtung:** keine; es gibt keine Route, mit der die Konditionen eines Mietverhältnisses später geändert werden, und eine Änderung der Angebotsmiete wirkt nicht auf bestehende Mietverhältnisse.
- **Status:** bestätigt
- **Entscheidung:** übernommen. Hat die Mieteinheit keine Angebotsmiete und ist keine Kaltmiete angegeben, wird das Anlegen abgelehnt (Kaltmiete ist Pflicht, fachmodell.md).
- **Offene Frage:** F-09

---

## REGEL-17 Übergaben protokollieren

- **Regel:** Zu jedem Mietverhältnis werden Einzug und Auszug je einmal als Übergabe mit Datum, Zählerständen, Schlüsselanzahl und Mängeln protokolliert.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-09 Übergabe protokollieren
- **Vorbedingung:** Einzug: Mietverhältnis *aktiv*, noch kein Einzug protokolliert. Auszug: siehe REGEL-21.
- **Invariante:** Je Mietverhältnis höchstens ein Einzug und höchstens ein Auszug. Das Datum eines Auszugs liegt nicht vor dem eines Einzugs.
- **Betroffene Objekte:** Mietverhältnis, Übergabe
- **Aggregat:** Mietverhältnis
- **Fundstellen:**
  - `src/Service/TenancyService.php::recordHandover()` – speichert die Übergabe
  - `src/Service/TenancyService.php::activate()` – plant den Termin für den Einzug
  - `src/Controller/TenancyController.php::terminate()`, `src/Service/TenancyService.php::terminateImmediately()` – planen den Termin für den Auszug
- **Gegenbeobachtung:** Die Art der Übergabe ist im Formular frei wählbar; mehrere Einzüge oder Auszüge zu einem Mietverhältnis sind möglich.
- **Status:** bestätigt
- **Entscheidung:** höchstens eine Übergabe je Art (fachmodell.md: Übergabe 0..2, lokale Identität ist die Art). Zählerstände und Mängel werden einzeln erfasst (F-19).
- **Offene Frage:** F-19

---

## REGEL-18 Vertragsnummer

- **Regel:** Jedes Mietverhältnis hat eine eindeutige Vertragsnummer, die beim Anlegen vergeben wird.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-04 Mietverhältnis als Entwurf anlegen
- **Invariante:** Vertragsnummern sind eindeutig und ändern sich nie.
- **Nachbedingung:** Neues Mietverhältnis: Vertragsnummer = `MV-` und die nächste Zahl der Folge (ab 01001).
- **Betroffene Objekte:** Mietverhältnis
- **Aggregat:** Mietverhältnis (Nummernvergabe)
- **Fundstellen:**
  - `src/Entity/Tenancy.php::contractNumber` – eindeutig
  - `src/Repository/TenancyRepository.php::nextContractNumber()` – Nummer = `MV-` + (1000 + Anzahl Mietverhältnisse + 1)
- **Gegenbeobachtung:** Wie bei REGEL-10 aus der Anzahl berechnet; gleichzeitiges Anlegen erzeugt doppelte Nummern.
- **Status:** bestätigt
- **Entscheidung:** wie REGEL-10: Nummer aus einer Folge, nicht aus der Anzahl.
- **Offene Frage:** Ist die Vertragsnummer für Mieter sichtbar? (F-25)

---

## REGEL-19 Kündigung eines Mietverhältnisses

- **Regel:** Ein aktives Mietverhältnis kann gekündigt werden; es endet nach Ablauf der Kündigungsfrist, und der Auszug wird für diesen Tag geplant.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-07 Mietverhältnis kündigen
- **Vorbedingung:** Mietverhältnisstatus *aktiv*; Kündigungsdatum angegeben und nicht vor dem Beginn der Laufzeit.
- **Nachbedingung:** Mietverhältnisstatus *gekündigt*; Kündigungsdatum gesetzt; Ende der Laufzeit = Kündigungsdatum + Kündigungsfrist; Mieteinheit *gekündigt* (REGEL-29); Auszugstermin am Ende der Laufzeit angelegt.
- **Betroffene Objekte:** Mietverhältnis, Mieteinheit, Termin
- **Aggregat:** Mietverhältnis (Folgen über Ereignisse)
- **Fundstellen:**
  - `src/Controller/TenancyController.php::terminate()` – nur bei aktivem Mietverhältnis; Ende = Kündigungsdatum + 3 Monate; Mietverhältnis und Mieteinheit werden »gekündigt«
  - `src/Service/AppointmentService.php::createHandover()` – Termin für den Auszug am Ende
- **Gegenbeobachtung:**
  - Die Frist steht fest im Controller, nicht in einem Service; sie ist für alle Mietverhältnisse gleich.
  - Ein Kündigungsdatum in der Zukunft oder weit in der Vergangenheit wird nicht geprüft.
- **Status:** zu klären
- **Entscheidung:** Die Frist gehört ins Modell: Attribut *Kündigungsfrist* des Mietverhältnisses, Vorgabe 3 Monate (B-05). Ein befristetes Mietverhältnis, dessen bisheriges Ende vor dem neuen liegt, behält das frühere Ende (Annahme).
- **Offene Frage:** F-10

---

## REGEL-20 Aufhebung eines Mietverhältnisses

- **Regel:** Nur die Administration darf ein aktives oder gekündigtes Mietverhältnis zu einem Stichtag aufheben.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-08 Mietverhältnis fristlos aufheben
- **Vorbedingung:** Mietverhältnisstatus *aktiv* oder *gekündigt*; der auslösende Benutzer hat die Rolle Administration; Stichtag ≥ Beginn der Laufzeit.
- **Nachbedingung:** Mietverhältnisstatus *aufgehoben*; Ende der Laufzeit = Stichtag; Mieteinheit *gekündigt* (REGEL-29); Auszugstermin am Stichtag angelegt.
- **Betroffene Objekte:** Mietverhältnis, Mieteinheit, Termin
- **Aggregat:** Mietverhältnis + Anwendungsschicht (Rolle)
- **Fundstellen:**
  - `templates/tenancy/show.html.twig` – Button »Aufhebung / fristlos« nur bei aktivem oder gekündigtem Mietverhältnis und nur für `ROLE_ADMIN`
  - `src/Service/TenancyService.php::terminateImmediately()` – Mietverhältnis »aufgehoben«, Ende = Stichtag, Mieteinheit »gekündigt«, Termin für den Auszug
- **Gegenbeobachtung:** `src/Controller/TenancyController.php::terminateImmediately()` und der Service prüfen weder Rolle noch Zustand; `config/packages/security.yaml` erlaubt die Route jedem angemeldeten Benutzer. Damit lassen sich auch Entwürfe und beendete Mietverhältnisse aufheben.
- **Status:** widersprüchlich
- **Entscheidung:** Zustand und Rolle werden in der Fachlogik geprüft, nicht nur in der Oberfläche (B-02). Ein Entwurf wird nicht aufgehoben, sondern verworfen (REGEL-31).
- **Offene Frage:** F-11

---

## REGEL-21 Auszug beendet das Mietverhältnis

- **Regel:** Mit dem Auszug endet ein gekündigtes oder aufgehobenes Mietverhältnis; die Mieteinheit wird wieder frei oder, wenn Mängel festgestellt wurden, in Renovierung gesetzt.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-09 Übergabe protokollieren (Auszug)
- **Vorbedingung:** Noch kein Auszug protokolliert, und Mietverhältnisstatus *gekündigt* oder *aufgehoben* – oder *aktiv*, wenn das Mietverhältnis befristet ist und das Auszugsdatum nicht vor dem Ende der Laufzeit liegt.
- **Nachbedingung:** Übergabe der Art *Auszug* protokolliert; Mietverhältnisstatus *beendet*; das Ende der Laufzeit bleibt unverändert. Mieteinheit *Renovierung*, wenn mindestens ein Mangel festgestellt wurde, sonst *frei* (REGEL-29).
- **Betroffene Objekte:** Mietverhältnis, Übergabe, Mieteinheit
- **Aggregat:** Mietverhältnis (Folgen über Ereignisse)
- **Fundstellen:**
  - `src/Service/TenancyService.php::recordHandover()` – bei Auszug Mietverhältnis »beendet«, Ende = Auszugsdatum (falls leer), Mieteinheit »frei« oder »Renovierung«
  - `src/Controller/TenancyController.php::handover()` – schlägt bei gekündigtem oder aufgehobenem Mietverhältnis »Auszug« vor
- **Gegenbeobachtung:** Der Zustand des Mietverhältnisses wird nicht geprüft; ein Auszug beendet auch ein aktives (nicht gekündigtes) Mietverhältnis und über einen direkten Aufruf sogar einen Entwurf.
- **Status:** widersprüchlich
- **Entscheidung:** Auszug ohne Kündigung nur bei befristetem Mietverhältnis (B-01, B-03). „Ende = Auszugsdatum, falls leer“ entfällt: In jedem erlaubten Fall ist das Ende bereits gesetzt.
- **Offene Frage:** F-12

---

## REGEL-22 Aufgabe nur mit Begründung wiedereröffnen

- **Regel:** Eine erledigte oder abgebrochene Aufgabe darf nur mit Begründung wiedereröffnet werden; die Begründung wird mit Datum in den Notizen festgehalten.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-23 Aufgabe wiedereröffnen
- **Vorbedingung:** Aufgabenstatus *erledigt* oder *abgebrochen*; Begründung nicht leer; eine zugehörige Meldung ist nicht *geschlossen* (REGEL-02).
- **Nachbedingung:** Aufgabenstatus *zugewiesen*, wenn ein Dienstleister eingetragen ist, sonst *offen*; die Notizen enthalten zusätzlich Datum und Begründung; die Meldung ist *in Bearbeitung* (REGEL-07).
- **Betroffene Objekte:** Aufgabe, Meldung
- **Aggregat:** Aufgabe + Anwendungsschicht
- **Fundstellen:**
  - `src/Controller/TaskController.php::reopen()` – verlangt bei erledigter Aufgabe eine Begründung, setzt »offen« und ergänzt die Notizen
  - `templates/task/edit.html.twig` – Button »Wieder eröffnen« nur bei nicht offener Aufgabe
- **Gegenbeobachtung:**
  - Eine abgebrochene Aufgabe kann ohne Begründung wiedereröffnet werden.
  - Über das Statusfeld in `src/Form/TaskType.php` kann jede Aufgabe ohne Begründung in jeden Status gesetzt werden.
  - `TaskController::done()` setzt »erledigt« ohne jede Prüfung, auch für abgebrochene Aufgaben.
- **Status:** widersprüchlich
- **Entscheidung:** Begründung auch bei abgebrochenen Aufgaben (B-21); Rückkehr nach *zugewiesen* bei eingetragenem Dienstleister (B-22); erlaubte Übergänge siehe verhalten.md und REGEL-34.
- **Offene Frage:** F-13

---

## REGEL-23 Ansprechpartner bei Dienstleister-Aufgaben

- **Regel:** Eine Aufgabe für einen Dienstleister braucht einen Ansprechpartner; ist ein Dienstleister eingetragen, gilt eine offene Aufgabe als zugewiesen.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-18, AF-21
- **Invariante:** Ist ein Dienstleister eingetragen, ist der Ansprechpartner nicht leer.
- **Nachbedingung:** Dienstleister eintragen: Ist kein Ansprechpartner angegeben, wird der des Dienstleisters übernommen; hat auch der Dienstleister keinen, wird die Aktion abgelehnt. War die Aufgabe *offen*, ist sie danach *zugewiesen*.
- **Betroffene Objekte:** Aufgabe; gelesen: Dienstleister
- **Aggregat:** Aufgabe + Anwendungsschicht
- **Fundstellen:**
  - `src/Service/TaskService.php::save()` – übernimmt den Ansprechpartner des Dienstleisters, lehnt ohne Ansprechpartner ab, setzt »offen« auf »zugewiesen«
  - `src/Service/IssueService.php::createTaskFromIssue()` – setzt bei vorgeschlagenem Dienstleister »zugewiesen«
- **Gegenbeobachtung:** `createTaskFromIssue()` prüft den Ansprechpartner nicht; hat der Dienstleister keinen, entsteht eine zugewiesene Aufgabe ohne Ansprechpartner.
- **Status:** widersprüchlich
- **Entscheidung:** Die Invariante gilt auch für den Vorschlag aus der Meldung: Vorgeschlagen werden nur Dienstleister mit Ansprechpartner (REGEL-24).
- **Offene Frage:** Ist der Ansprechpartner Pflicht, oder genügt der Dienstleister? (F-26)

---

## REGEL-24 Nur aktive Dienstleister, Vorschlag nach Gewerk

- **Regel:** Aufgaben dürfen nur aktiven Dienstleistern zugeordnet werden; zu einer Meldung wird ein Dienstleister mit passendem Gewerk vorgeschlagen.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-18 Aufgabe zu einer Meldung anlegen, AF-21 Aufgabe anlegen und pflegen
- **Vorbedingung:** Dienstleister eintragen: Der Dienstleister ist *aktiv*.
- **Nachbedingung:** Aufgabe zu einer Meldung ohne angegebenen Dienstleister: Gibt es zur Kategorie ein Gewerk (Heizung → Heizung, Wasser/Sanitär → Sanitär, Elektrik/Licht → Elektro, Aufzug → Aufzug) und dazu einen aktiven Dienstleister mit Ansprechpartner, ist er eingetragen. Bei mehreren gilt der erste nach Firmenname (Annahme).
- **Betroffene Objekte:** Aufgabe; gelesen: Meldung, Dienstleister
- **Aggregat:** Aufgabe + Anwendungsschicht
- **Fundstellen:**
  - `src/Form/TaskType.php` – Auswahl nur aus aktiven Dienstleistern (`ServiceProviderRepository::createActiveQueryBuilder()`)
  - `src/Service/IssueService.php::createTaskFromIssue()` – Vorschlag nach Kategorie der Meldung (Heizung, Sanitär, Elektro, Aufzug)
- **Gegenbeobachtung:** Der Vorschlag in `createTaskFromIssue()` sucht nur nach dem Gewerk und berücksichtigt den Status nicht; ein inaktiver Dienstleister kann vorgeschlagen werden. Bei mehreren passenden wird der erste genommen.
- **Status:** widersprüchlich
- **Entscheidung:** Nur aktive Dienstleister, auch beim Vorschlag. Die Zuordnung Kategorie → Gewerk ist Teil des Modells. Ein später inaktiv gesetzter Dienstleister bleibt bei bestehenden Aufgaben eingetragen.
- **Offene Frage:** Nach welchen Kriterien soll unter mehreren passenden Dienstleistern ausgewählt werden? (F-27)

---

## REGEL-25 Zusage an einen Mietinteressenten

- **Regel:** Nach einer Zusage wird der Mietinteressent als Mieter übernommen; ist die gewünschte Mieteinheit frei, wird sie für ihn reserviert.
- **Bereich:** Vermietung
- **Anwendungsfall:** AF-03 Zusage erteilen
- **Vorbedingung:** Interessentenstatus *neu*, *Besichtigung geplant* oder *Bewerbung eingegangen*; Vor- und Nachname des künftigen Mieters sind angegeben (REGEL-30).
- **Nachbedingung:** Interessentenstatus *Zusage*; ein neuer Mieter mit diesem Namen und den Kontaktdaten des Mietinteressenten besteht; war die gewünschte Mieteinheit *frei*, ist sie *reserviert*.
- **Betroffene Objekte:** Mietinteressent, Mieter, Mieteinheit
- **Aggregat:** Mietinteressent + Anwendungsschicht
- **Fundstellen:**
  - `src/Service/RentalService.php::acceptProspect()` – legt den Mieter an, Interessentenstatus »Zusage«, reserviert nur eine freie Mieteinheit
  - `templates/letting/prospects.html.twig` – Button »Zusage« nur bei Status neu, Besichtigung geplant oder Bewerbung eingegangen
  - `src/Controller/LettingController.php::accept()` – leitet zur Anlage des Mietverhältnisses weiter
- **Gegenbeobachtung:**
  - Der Service prüft den Interessentenstatus nicht; eine zweite Zusage legt den Mieter ein zweites Mal an.
  - Der Name wird am letzten Leerzeichen in Vor- und Nachname getrennt; bei einem einzigen Wort ist der Vorname »-«.
  - Die Reservierung hat kein Ablaufdatum und wird nicht aufgehoben, wenn kein Mietverhältnis folgt.
- **Status:** widersprüchlich
- **Entscheidung:** Der Interessentenstatus wird in der Fachlogik geprüft; eine zweite Zusage ist nicht möglich (B-13). Vor- und Nachname werden bei der Zusage angegeben (vorbelegt aus dem Namen), statt automatisch getrennt. Die Reservierung kann aufgehoben werden (REGEL-31).
- **Offene Frage:** F-14

---

## REGEL-26 Liegenschaft gehört genau einem Eigentümer

- **Regel:** Jede Liegenschaft gehört genau einem aktiven Eigentümer.
- **Bereich:** Bestand
- **Anwendungsfall:** AF-10 Liegenschaft anlegen und pflegen
- **Invariante:** Jede Liegenschaft hat genau einen Eigentümer.
- **Vorbedingung:** Eigentümer zuordnen oder wechseln: Der Eigentümer ist *aktiv*.
- **Betroffene Objekte:** Liegenschaft; gelesen: Eigentümer
- **Aggregat:** Liegenschaft + Anwendungsschicht
- **Fundstellen:**
  - `src/Entity/Property.php::owner` – Pflichtverweis auf einen Eigentümer
  - `src/Form/PropertyType.php` – Auswahl des Eigentümers
- **Gegenbeobachtung:**
  - Auch inaktive Eigentümer sind auswählbar; der Eigentümerstatus wird nirgends ausgewertet.
  - Ein Eigentümerwechsel ist durch Bearbeiten jederzeit möglich, ohne Stichtag; nur Buchungen merken sich den Eigentümer zum Buchungszeitpunkt (`src/Controller/AccountingController.php::newBooking()`).
- **Status:** widersprüchlich
- **Entscheidung:** Inaktive Eigentümer können nicht neu zugeordnet werden. Der Eigentümerwechsel ohne Stichtag bleibt wie im Altsystem, bis F-15 beantwortet ist.
- **Offene Frage:** F-15

---

## REGEL-27 Objekt-, Gebäude- und Eigentümernummer

- **Regel:** Liegenschaften, Gebäude und Eigentümer erhalten beim Anlegen eine Nummer: Objektnummer `OB-0xxx`, Gebäudenummer aus Objektnummer und laufender Gebäudezahl (`OB-0xxx-G1`), Eigentümernummer `EI-xxxx`.
- **Bereich:** Bestand
- **Anwendungsfall:** AF-10 Liegenschaft, AF-11 Gebäude, AF-13 Eigentümer anlegen
- **Invariante:** Objekt-, Gebäude- und Eigentümernummern sind jeweils eindeutig und ändern sich nach dem Anlegen nicht.
- **Nachbedingung:** Neue Liegenschaft: Objektnummer = `OB-` und die nächste Zahl der Folge (ab 0101). Neues Gebäude: Gebäudenummer = Objektnummer + `-G` + (höchste bisherige Gebäudezahl der Liegenschaft + 1). Neuer Eigentümer: Eigentümernummer = `EI-` und die nächste Zahl der Folge (ab 0001).
- **Betroffene Objekte:** Liegenschaft, Gebäude, Eigentümer
- **Aggregat:** Liegenschaft bzw. Eigentümer (Nummernvergabe)
- **Fundstellen:**
  - `src/Service/PropertyService.php::nextPropertyNumber()` – Objektnummer = Anzahl Liegenschaften + 101
  - `src/Controller/PropertyController.php::newBuilding()` – Gebäudenummer
  - `src/Controller/OwnerController.php::new()` – Eigentümernummer = Anzahl Eigentümer + 1
  - `src/Entity/Property.php::propertyNumber`, `src/Entity/Owner.php::ownerNumber` – eindeutig
- **Gegenbeobachtung:**
  - Die Nummern sind nur Vorschläge und im Formular änderbar; eine geänderte Nummer kann mit einem späteren Vorschlag kollidieren.
  - Die Gebäudenummer ist nicht eindeutig.
- **Status:** widersprüchlich
- **Entscheidung:** Die Nummern vergibt das System; sie sind nicht änderbar, weil sie die Identität sind (fachmodell.md). Gebäudezahl aus der höchsten statt aus der Anzahl, damit keine Nummer doppelt entsteht.
- **Offene Frage:** Sind die Nummern frei vergebbar oder fest vom System vergeben? (F-28)

---

## REGEL-28 Hierarchie Liegenschaft – Gebäude – Mieteinheit

- **Regel:** Ein Gebäude gehört zu genau einer Liegenschaft und übernimmt beim Anlegen deren Anschrift; eine Mieteinheit gehört zu genau einem Gebäude und ist dort an ihrer Einheitsnummer erkennbar.
- **Bereich:** Bestand
- **Anwendungsfall:** AF-11 Gebäude anlegen und pflegen, AF-12 Mieteinheit anlegen und pflegen
- **Invariante:** Jedes Gebäude gehört genau einer Liegenschaft, jede Mieteinheit genau einem Gebäude; beides ändert sich nicht. Innerhalb eines Gebäudes ist die Einheitsnummer eindeutig.
- **Nachbedingung:** Gebäude anlegen ohne Anschrift: Anschrift = Anschrift der Liegenschaft.
- **Betroffene Objekte:** Liegenschaft, Gebäude, Mieteinheit
- **Aggregat:** Liegenschaft (Gebäude) bzw. Mieteinheit + Anwendungsschicht
- **Fundstellen:**
  - `src/Entity/Building.php::property`, `src/Entity/RentalUnit.php::building` – Pflichtverweise
  - `src/Controller/PropertyController.php::newBuilding()` – übernimmt Straße und Hausnummer der Liegenschaft
  - `src/Controller/RentalUnitController.php::new()` – Mieteinheit wird für ein Gebäude angelegt
- **Gegenbeobachtung:**
  - Die Einheitsnummer ist weder je Gebäude noch insgesamt eindeutig.
  - Über `src/Form/RentalUnitType.php` kann eine Mieteinheit einem anderen Gebäude, auch einer anderen Liegenschaft, zugeordnet werden – auch während sie vermietet ist.
- **Status:** widersprüchlich
- **Entscheidung:** Eine Mieteinheit wechselt das Gebäude nicht, weil Gebäude + Einheitsnummer ihre Identität ist (fachmodell.md). Die Einheitsnummer ist je Gebäude eindeutig (Annahme).
- **Offene Frage:** F-16

---

## REGEL-29 Einheitenstatus folgt der Vermietung

- **Regel:** Der Einheitenstatus einer Mieteinheit folgt aus der Vermietung: reserviert bei Entwurf oder Zusage, vermietet bei aktivem, gekündigt bei gekündigtem oder aufgehobenem Mietverhältnis, frei oder Renovierung nach dem Auszug.
- **Bereich:** Bestand
- **Anwendungsfall:** AF-03, AF-04, AF-06 bis AF-09, AF-12
- **Invariante:**
  - *vermietet* ⇔ die Mieteinheit hat ein Mietverhältnis im Zustand *aktiv*;
  - *gekündigt* ⇔ sie hat ein Mietverhältnis im Zustand *gekündigt* oder *aufgehoben*;
  - *reserviert* ⇔ sie hat einen Entwurf oder eine Reservierung aus einer Zusage, aber kein Mietverhältnis in den beiden Fällen davor;
  - *frei*, *Renovierung* oder *gesperrt* nur ohne Mietverhältnis in den Zuständen Entwurf, aktiv, gekündigt oder aufgehoben.
- **Betroffene Objekte:** Mieteinheit, Mietverhältnis, Mietinteressent
- **Aggregat:** Mieteinheit (Reaktion auf Ereignisse)
- **Fundstellen:**
  - `src/Service/TenancyService.php::createTenancy()`, `activate()`, `terminateImmediately()`, `recordHandover()`
  - `src/Controller/TenancyController.php::terminate()`
  - `src/Service/RentalService.php::acceptProspect()`
- **Gegenbeobachtung:**
  - In `src/Form/RentalUnitType.php` ist der Einheitenstatus frei wählbar; eine vermietete Mieteinheit kann ohne Mietverhältnis auf »frei« gesetzt werden und umgekehrt.
  - »Renovierung → frei« und »gesperrt« sind nur über dieses Formular erreichbar.
- **Status:** widersprüchlich
- **Entscheidung:** Der Einheitenstatus wird nicht frei gesetzt. Von Hand gibt es nur *sperren*, *entsperren* und *Renovierung abschließen* (B-07); alle anderen Wechsel folgen aus Übergängen des Mietverhältnisses bzw. der Zusage. Weil Mieteinheit und Mietverhältnis in verschiedenen Kontexten liegen, wird die Regel über Ereignisse eingehalten (Schritt 8).
- **Offene Frage:** F-17

---

## REGEL-30 Mieter hat Vor- und Nachnamen

- **Regel:** Ein Mieter hat einen Vor- und einen Nachnamen.
- **Bereich:** Bestand
- **Anwendungsfall:** AF-14 Mieter pflegen, AF-03 Zusage erteilen
- **Invariante:** Vorname und Nachname eines Mieters sind nicht leer.
- **Betroffene Objekte:** Mieter
- **Aggregat:** Mieter
- **Fundstellen:**
  - `src/Entity/Tenant.php::firstName`, `lastName` – Pflichtfelder
  - `src/Form/TenantType.php` – Erfassung
- **Gegenbeobachtung:** `src/Service/RentalService.php::acceptProspect()` setzt bei einem einteiligen Namen den Vornamen »-«; der Mietinteressent hat nur ein Namensfeld.
- **Status:** widersprüchlich
- **Entscheidung:** Die Invariante gilt auch bei der Zusage; der Platzhalter »-« ist kein Vorname (REGEL-25). Firmen als Mieter sind nicht vorgesehen, solange F-07 offen ist.
- **Offene Frage:** F-07

---

## REGEL-31 Entwurf verwerfen und Reservierung aufheben

- **Regel:** Ein Mietverhältnis im Entwurf kann verworfen und eine Reservierung aufgehoben werden; danach ist die Mieteinheit wieder frei, wenn nichts anderes sie reserviert.
- **Bereich:** Vermietung
- **Anwendungsfall:** neu (zu AF-04); Reservierung aufheben neu (zu AF-03)
- **Vorbedingung:** Verwerfen: Mietverhältnisstatus *Entwurf*. Reservierung aufheben: Einheitenstatus *reserviert* und kein Entwurf für die Mieteinheit.
- **Nachbedingung:** Verwerfen: Das Mietverhältnis besteht nicht mehr. Die Mieteinheit ist *frei*, wenn es keinen weiteren Entwurf für sie gibt, sonst bleibt sie *reserviert*. Reservierung aufheben: Mieteinheit *frei*.
- **Betroffene Objekte:** Mietverhältnis, Mieteinheit
- **Aggregat:** Mietverhältnis (verwerfen) bzw. Mieteinheit (Reservierung aufheben) + Anwendungsschicht
- **Fundstellen:** keine – fehlt im Altsystem (verhalten.md B-04, B-08)
- **Gegenbeobachtung:** Ohne diese Aktionen bleibt eine Mieteinheit dauerhaft reserviert, wenn aus Zusage oder Entwurf kein aktives Mietverhältnis wird; der einzige Ausweg ist das freie Statusfeld in `src/Form/RentalUnitType.php`.
- **Status:** neu
- **Entscheidung:** eingeführt, damit jeder Zustand einen Weg hinaus hat (Prüffrage Schritt 6). Ob ein verworfener Entwurf zur Nachverfolgung erhalten bleibt, ist offen.
- **Offene Frage:** F-11, F-14

---

## REGEL-32 Meldung nur mit Kommentar ablehnen

- **Regel:** Eine Meldung darf nur abgelehnt werden, solange sie neu oder in Prüfung ist und keine Aufgaben hat, und nur mit Kommentar.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-17 Meldungsstatus ändern (ablehnen)
- **Vorbedingung:** Meldungsstatus *neu* oder *in Prüfung*; die Meldung hat keine Aufgaben; ein Kommentar ist angegeben.
- **Nachbedingung:** Meldungsstatus *abgelehnt*; ein Statuswechsel mit diesem Kommentar ist eingetragen (REGEL-09).
- **Betroffene Objekte:** Meldung; gelesen: Aufgabe
- **Aggregat:** Meldung + Anwendungsschicht
- **Fundstellen:** `src/Controller/IssueController.php::status()` – Ablehnen nur über die freie Statusänderung, Kommentar optional
- **Gegenbeobachtung:** Jede Meldung kann in jedem Zustand und ohne Kommentar abgelehnt werden, auch mit offenen Aufgaben.
- **Status:** neu
- **Entscheidung:** Ablehnen wird eine eigene Aktion (B-17). Der Kommentar ist Pflicht, weil der Mieter eine Begründung erhalten soll (Annahme).
- **Offene Frage:** F-05

---

## REGEL-33 Aufgabe nur mit Begründung abbrechen

- **Regel:** Eine nicht abgeschlossene Aufgabe darf nur mit Begründung abgebrochen werden; die Begründung wird mit Datum in den Notizen festgehalten.
- **Bereich:** Meldungen/Aufgaben
- **Anwendungsfall:** AF-21 Aufgabe pflegen (abbrechen)
- **Vorbedingung:** Aufgabenstatus *offen*, *zugewiesen*, *in Bearbeitung* oder *wartet*; Begründung nicht leer.
- **Nachbedingung:** Aufgabenstatus *abgebrochen*; die Notizen enthalten zusätzlich Datum und Begründung.
- **Betroffene Objekte:** Aufgabe
- **Aggregat:** Aufgabe
- **Fundstellen:** `src/Form/TaskType.php` – Abbrechen nur über das freie Statusfeld
- **Gegenbeobachtung:** Abbrechen ist ohne Begründung möglich und wird nicht festgehalten.
- **Status:** neu
- **Entscheidung:** eigene Aktion (B-23), aus Symmetrie zum Wiedereröffnen (REGEL-22).
- **Offene Frage:** F-13

---

## REGEL-34 Zustände ändern sich nur über erlaubte Übergänge

- **Regel:** Mietverhältnisstatus, Einheitenstatus, Interessentenstatus, Besichtigungsstatus, Meldungsstatus und Aufgabenstatus ändern sich nur über die Übergänge in verhalten.md, nie durch direktes Setzen.
- **Bereich:** Vermietung, Bestand, Meldungen/Aufgaben
- **Anwendungsfall:** alle, die einen Status ändern
- **Invariante:** Jeder Statuswechsel entspricht einer Zeile *Modell* in verhalten.md und erfüllt deren Bedingung.
- **Betroffene Objekte:** Mietverhältnis, Mieteinheit, Mietinteressent, Besichtigung, Meldung, Aufgabe
- **Aggregat:** jedes Aggregat für seinen eigenen Status
- **Fundstellen:** verhalten.md, Abschnitte *Modell*
- **Gegenbeobachtung:**
  - `src/Form/RentalUnitType.php`, `src/Form/ProspectType.php`, `src/Form/TaskType.php` – Status frei wählbar
  - `src/Controller/IssueController.php::status()`, `bulk()` – Meldungsstatus frei wählbar
  - `src/Service/TenancyService.php::terminateImmediately()`, `recordHandover()` – Zustand nicht geprüft
- **Status:** neu
- **Entscheidung:** fasst B-07, B-11, B-15 und B-20 zusammen. Eine Bearbeitungsmaske ändert nur Stammdaten, nie den Status.

---

## REGEL-35 Verwaltung einer Liegenschaft beenden

- **Regel:** Die Verwaltung einer Liegenschaft darf nur beendet werden, wenn in ihr kein Mietverhältnis mehr läuft.
- **Bereich:** Bestand
- **Anwendungsfall:** AF-10 Liegenschaft pflegen
- **Vorbedingung:** Keine Mieteinheit der Liegenschaft hat ein Mietverhältnis im Zustand *Entwurf*, *aktiv*, *gekündigt* oder *aufgehoben*.
- **Nachbedingung:** Verwaltungsstatus *beendet*.
- **Betroffene Objekte:** Liegenschaft; gelesen: Mietverhältnisse ihrer Mieteinheiten
- **Aggregat:** Liegenschaft + Anwendungsschicht
- **Fundstellen:** `src/Form/PropertyType.php` – Verwaltungsstatus frei wählbar
- **Gegenbeobachtung:** Das Altsystem prüft nichts; eine Liegenschaft mit laufenden Mietverhältnissen kann auf »Verwaltung beendet« gesetzt werden.
- **Status:** neu
- **Entscheidung:** Annahme bis zur Antwort auf F-21.
- **Offene Frage:** F-21
