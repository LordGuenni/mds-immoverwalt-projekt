# Logbuch

Entscheidungen, Begründungen, verworfene Wege und KI-Einsatz der Projektgruppe.
Neueste Einträge unten. Verweise: Regeln `REGEL-xx` in [regelkatalog.md](./regelkatalog.md),
Anwendungsfälle `AF-xx` in [anwendungsfaelle.md](./anwendungsfaelle.md).

> Die Einträge bis einschließlich 2026-10-20 wurden am 2026-10-27 **nachträglich aus der
> Git-Historie rekonstruiert**. Sie geben wieder, was in den Dateien steht; Begründungen,
> die nur mündlich besprochen wurden, sind als *nachzutragen* markiert.

---

## 2026-10-12 – Projekt-Repository (Ü2, Anleitung)

- Repository aus der Vorlage `thb_immoverwalt` angelegt; Vorlage als Remote `vorlage` behalten.
- Eigenes Docker-Image und GitHub-Workflow angepasst (Commits `5c8f4df`, `249d0f3`).
- Begründung: *nachzutragen* (z. B. Architektur des Rechners, Build-Zeit).

## 2026-10-13 – Regelkatalog (Ü2, Aufgabe A und B)

- REGEL-02 (Meldung schließen) aus `IssueService`, `IssueController` und `issue/show.html.twig` abgeleitet.
- REGEL-03 bis REGEL-18 ergänzt: Meldungen/Aufgaben (03–11) und Mietverhältnis (12–18, Aufgabe B).
- Aufgabe B: Feld `noticeGiven` (Ticket #213) als Doppelung zum Zustand erkannt → REGEL-15.
- Aufgabe C (Ebenen M0–M3) und Aufgabe D (erstes Fachmodell) wurden nicht abgelegt; siehe Eintrag 2026-10-27.

## 2026-10-20 – Leitfaden Schritte 1 bis 4 (Ü3)

- **Schritt 1:** `debug:router` (64 Routen) und `doctrine:mapping:info` (18 Entitäten) → `bestand.md`.
  Alle Routen erfasst, nicht nur die Pflichtbereiche, um später nichts nachholen zu müssen.
- **Schritt 2:** 29 Anwendungsfälle → `anwendungsfaelle.md`; Reihenfolge nach Bereichen sortiert (`88215dd`).
- **Schritt 3:** Glossar mit 22 Begriffen. Entscheidungen:
  - *Mietverhältnis* statt *Mietvertrag*: Mietvertrag ist das Dokument, Mietverhältnis die Beziehung.
  - *Liegenschaft* statt *Objekt*/*Immobilie*: „Objekt“ ist mehrdeutig.
  - *Meldung* statt *Schadensmeldung*: auch Beschwerden sind Meldungen (Kategorie `BESCHWERDE`).
- **Schritt 4:** Sechs Kontexte (Vermietung, Bestand, Meldungen und Aufgaben, Termine, Abrechnung, Berichte).
  Termine als eigener Kontext, weil Termine von Vermietung und Meldungen gleichermaßen erzeugt werden und
  keinen eigenen Lebenszyklus der referenzierten Objekte besitzen. Begründung im Detail: *nachzutragen*.

---

## 2026-10-27 – Prüfung und Bereinigung vor Ü4

**KI-Einsatz:** Claude (Claude Code) hat `model/` gegen den Code geprüft und die Korrekturen
vorgeschlagen und eingetragen. Jede Aussage wurde an der genannten Fundstelle im Code belegt.
Die Gruppe prüft die Änderungen per `git diff` und übernimmt oder verwirft sie.

### Schritt 2 erneut geprüft (`anwendungsfaelle.md`)

- Routenabdeckung per Skript geprüft: 64 Routen, jede genau einmal → in Ordnung.
- `tenancy_show` war als Route von AF-04 gezählt, ist aber eine reine Detailansicht wie alle anderen
  `*_show`-Routen → zu den Lese-Ansichten verschoben (jetzt 40 + 24 = 64).
- **Akteure:** Das Altsystem setzt die Rollen nicht durch (`security.yaml`: alles außer `/verwaltung`
  für `ROLE_USER`). Entscheidung: Die Spalte *Akteur* bleibt als fachliche Zuordnung, wird aber
  ausdrücklich als Annahme gekennzeichnet. Verworfen: Akteur „jeder Benutzer“ – das beschreibt den
  Fehler des Altsystems, nicht die Fachlichkeit.
- Inhaltliche Fehler korrigiert, die nicht zum Code passten:
  - Eigentümer haben **keine** Bankverbindung und keine Steuernummer (`Owner.php`).
  - Liegenschaften haben **keinen** Honorarsatz; das Verwaltungshonorar ist pauschal 5 % in
    `config/services.yaml` (`app.management_fee_percent`).
  - Mieteinheiten haben **keine** Zimmerzahl und es gibt **keine** Stellplätze (`type` nur `WHG`/`GEW`).
  - AF-09 „sperren“ war falsch: nach dem Auszug wird die Einheit frei oder in Renovierung gesetzt.
  - AF-06 erzeugt nur die **erste** Sollstellung, nicht alle monatlichen.
  - AF-04: Der Button erscheint nur bei freien Einheiten; reservierte Einheiten erreicht man nur über
    die Weiterleitung nach einer Zusage.
  - Button-Beschriftungen an die Templates angeglichen.
- Namen an das Glossar angepasst: „Mietvertragsentwurf“ → „Mietverhältnis als Entwurf anlegen“,
  „Schadensmeldung“ → „Meldung“; Akteur „Verwaltung“ → „Verwalter“.
- Absoluten lokalen Link (`file:///home/florian/...`) durch relativen Link ersetzt.
- LaTeX-Formel im Abgleich durch Text ersetzt (wird beim PDF-Export nicht zuverlässig dargestellt).
- `kontexte.md`: Namen der Anwendungsfälle nachgezogen.

### `bestand.md`

- Dieselben Fehler (Bankverbindung, Verwaltergebühr, Zimmer, Stellplatz) korrigiert.
- Nummernformate an den Code angepasst: `OB-0xxx` (nicht `OBJ-`), `ME-02xxx` (nicht `MEL-`),
  `AU-03xxx` (nicht `AUF-`), `MV-01xxx`.
- `task_new` setzt `ASSIGNED` nur, wenn ein Dienstleister eingetragen ist.

### Glossar

- Mieteinheit ohne „Stellplatz“.
- **Angebotsmiete** neu eingeführt: `RentalUnit::coldRent` und `Tenancy::coldRent` heißen im Code
  gleich, bedeuten aber Verschiedenes (Angebot der Einheit vs. vereinbarte Miete). Ein Begriff für
  beides würde im Fachmodell zu einem falschen Doppel-Attribut führen.
- **Kündigungsfrist** neu eingeführt, obwohl sie im Altsystem nur als `+3 months` vorkommt – sie ist
  ein fachlicher Begriff, der ins Modell gehört (Ü4, REGEL-19).
- „Status“ wird nur noch mit Zusatz verwendet (Mietverhältnis-, Einheiten-, Meldungs-, Aufgaben-,
  Interessentenstatus).
- Weitere Begriffe für Ü4 ergänzt: Nutzungsart, Kaltmiete, Nebenkostenvorauszahlung, Gesamtmiete,
  Kaution, Laufzeit, Kündigung, Kündigungsdatum, Aufhebung, Einzug/Auszug, Zählerstand, Mangel, Zusage,
  Priorität, Kategorie, Gewerk, Ansprechpartner, Fälligkeit, Statushistorie, Bearbeiter, Verwalter,
  Vermieter, Verwaltungshonorar.

### Regelkatalog

- **REGEL-02 und REGEL-06 zusammengeführt**: beide beschrieben „Meldung nur ohne offene Aufgaben
  schließen“, waren aber unterschiedlich bewertet. Der Inhalt steht jetzt in REGEL-02; REGEL-06 bleibt
  als Verweis stehen, damit keine Nummer neu vergeben wird.
- „Issue“, „geloggt“ und andere technische Wörter aus den Regelsätzen entfernt.
- Gegenbeobachtungen, die nur Meta-Kommentare waren („ist nicht als eigenes Konzept modelliert“),
  durch tatsächliche Beobachtungen am Code ersetzt oder mit „keine“ belegt.
- Korrigierte Bewertungen:
  - REGEL-09 **widersprüchlich** statt bestätigt: `closeIssue()`, `createTaskFromIssue()` und `bulk()`
    ändern den Status ohne Eintrag in der Statushistorie.
  - REGEL-15 korrigiert: Die ordentliche Kündigung setzt `noticeGiven` **nicht**, nur die Aufhebung.
    Entscheidung: `noticeGiven` wird nicht ins Modell übernommen.
  - REGEL-12, 13, 14, 07, 03 aufgrund neuer Gegenbeobachtungen auf widersprüchlich gesetzt.
- REGEL-01 vs. REGEL-12 abgegrenzt: Einheitenstatus bei Anlage vs. zeitliche Überschneidung.
- Pfade korrigiert (`templates/...` statt `src/templates/...`, Dateiendungen ergänzt).
- **Neue Regeln:** REGEL-19 bis REGEL-25 (Vermietung, Meldungen/Aufgaben) aus den Befunden zu den
  Zustandsübergängen; REGEL-26 bis REGEL-30 für den **Pflichtbereich Bestand**, der bisher keine
  einzige Regel hatte.
- Nummerierung: Die Beispiele in Leitfaden und Ü4 nennen die Kündigungsregel REGEL-08 bzw. REGEL-11.
  Diese Nummern sind bei uns belegt; die Kündigungsregel ist deshalb REGEL-19.

### Ü2 Aufgabe C – Ebenen M0 bis M3 (Entwurf, von der Gruppe zu prüfen)

| Element | Ebene | Begründung |
|---|---|---|
| Datensatz »Anna Müller« in der Tabelle `tenant` | M0 | konkretes Objekt der realen Anwendung zur Laufzeit |
| Klasse *Mieter* im Fachmodell | M1 | Element unseres Modells, beschreibt alle Mieter |
| UML-»Class«, Ecore-`EClass`, unser Konzept »Entity« | M2 | Sprachelemente, mit denen M1-Modelle geschrieben werden |
| Aufzählungsliteral `RENTED` in *EinheitStatus* | M1 | Teil der Aufzählung im Modell; erst der Status einer konkreten Einheit ist M0 |
| Status der Einheit WE 03 links am 1.10.2026 | M0 | Wert eines konkreten Objekts zu einem Zeitpunkt |
| Attribut *fläche* · Wert 78,20 | M1 · M0 | das Attribut steht im Modell, der Wert gehört zu einer konkreten Einheit |
| REGEL-01 · Format eines Regelkatalog-Eintrags | M1 · M2 | die Regel ist Teil unseres Modells; das Format legt fest, wie Regeln beschrieben werden |

**Unsicher:** `RENTED` – man könnte argumentieren, ein Literal sei ein Wert (M0). Es ist aber Teil der
Typdefinition im Modell und existiert unabhängig von jeder konkreten Einheit, deshalb M1.
M3 (MOF/Ecore-Metametamodell) kommt in der Liste nicht vor.

### Offen für Ü4

- `fachmodell.md` ist noch die Vorlage (Ü2 Aufgabe D). Wird mit Leitfaden Schritt 5 vollständig erstellt.
- `verhalten.md` (Schritt 6) neu anlegen; Grundlage sind die Fundstellen in REGEL-02, 07, 14, 15, 19–22, 25, 29.
- Regeln in Schritt 7 um Vor-/Nachbedingung bzw. Invariante, betroffene Objekte und Entscheidung erweitern.

---

## Offene Fragen an den Fachbereich

| Nr. | Frage | Regel |
|---|---|---|
| F-01 | Darf für eine reservierte Mieteinheit ein Mietverhältnis angelegt werden, und wer darf reservieren? | REGEL-01, REGEL-25 |
| F-02 | Darf eine Meldung mit offenen Aufgaben in Ausnahmefällen geschlossen werden? Was passiert, wenn eine Aufgabe einer geschlossenen Meldung wiedereröffnet wird? | REGEL-02 |
| F-03 | Welcher Mieter gilt bei mehreren Mietern als meldende Person? | REGEL-03 |
| F-04 | Ist Priorität »hoch« ebenfalls dringend? Gilt die Prüfungsregel auch bei späterer Erhöhung? | REGEL-05, REGEL-08 |
| F-05 | Muss bei jedem Statuswechsel einer Meldung ein Kommentar angegeben werden? | REGEL-09 |
| F-06 | Darf es für eine Mieteinheit mehrere Entwürfe gleichzeitig geben? | REGEL-12 |
| F-07 | Kann ein Mieter eine Firma sein? | REGEL-14, REGEL-30 |
| F-08 | Wofür wurde `noticeGiven` (Ticket #213) eingeführt? | REGEL-15 |
| F-09 | Wie werden Mieterhöhungen abgebildet? | REGEL-16 |
| F-10 | Gilt die Kündigungsfrist von drei Monaten für alle Kündigungen und Nutzungsarten? | REGEL-19 |
| F-11 | Wer darf ein Mietverhältnis aufheben? Ist das Aufheben eines Entwurfs ein »Verwerfen«? | REGEL-20 |
| F-12 | Ist ein Auszug ohne vorherige Kündigung zulässig (befristete Mietverhältnisse)? | REGEL-21 |
| F-13 | Welche Statuswechsel einer Aufgabe sind erlaubt? | REGEL-22 |
| F-14 | Wie lange gilt eine Reservierung, und wer hebt sie auf? | REGEL-25 |
| F-15 | Eigentümergemeinschaften und Eigentümerwechsel zu einem Stichtag? | REGEL-26 |
| F-16 | Woran wird eine Mieteinheit fachlich eindeutig erkannt? | REGEL-28 |
| F-17 | Welche Einheitenstatus darf der Verwalter direkt setzen? | REGEL-29 |
