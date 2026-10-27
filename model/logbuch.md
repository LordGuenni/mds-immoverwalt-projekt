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

## 2026-10-27 – Ü4: Vorgehen und Schritt 5 (Bausteine)

**KI-Einsatz:** Claude (Claude Code) hat das Vorgehen für Ü4 gegen Leitfaden und Übungsblatt
geprüft, die Felder aller Entitäten der Pflichtbereiche ausgelesen (Spaltentypen, Pflichtfelder,
Formularbeschriftungen) und daraus `fachmodell.md` entworfen. Die Gruppe prüft die Entscheidungen
unten und übernimmt oder verwirft sie.

### Vorgehen für Ü4 (geprüft)

| Schritt | Ergebnis | Umfang |
|---|---|---|
| 5 Bausteine | `fachmodell.md` | 13 Entitäten der Pflichtbereiche, je Kontext Tabelle und Klassendiagramm |
| 6 Zustände | `verhalten.md` | Mietverhältnis, Mieteinheit, Mietinteressent, Besichtigung, Meldung, Aufgabe |
| 7 Regeln | `regelkatalog.md` | alle 30 Regeln mit Vor-/Nachbedingung oder Invariante, betroffenen Objekten, Entscheidung |
| 8 Aggregate | `fachmodell.md`, Abschnitt Aggregate | jede Regel genau einem Aggregat oder der Anwendungsschicht zugeordnet |

Korrekturen am ersten Plan:

- **Schritt 6:** Der Befehl `grep "setStatus("` aus dem Leitfaden reicht nicht. Einheiten-, Aufgaben-
  und Interessentenstatus lassen sich zusätzlich frei über die Bearbeiten-Formulare setzen
  (`RentalUnitType`, `TaskType`, `ProspectType`), der Meldungsstatus über `issue_status` und
  `issue_bulk`. Zusätzlich auszuwerten: `grep -rn "'status'" src/Form`.
- **Reihenfolge:** Schritt 7 kann Schritt 5 ändern (Beispiel im Leitfaden: „Frist gehört ins
  Modell“). Die Kündigungsfrist wurde deshalb schon in Schritt 5 als Attribut aufgenommen.
- **Glossar:** Jeder neue Begriff aus dem Fachmodell kommt sofort ins Glossar, damit die Prüffrage
  aus Schritt 3 erfüllt bleibt.
- **Umfang:** Abrechnung (optional) und Termine werden nicht modelliert, nur als Verweis genannt.
- **PDF-Export** (`fachmodell.pdf` usw.): Werkzeug ist noch offen; auf dem Rechner sind weder
  `pandoc` noch `mermaid-cli` installiert.

### Schritt 5 – Entscheidungen

- **Ein Tabellenblock und ein Diagramm je Kontext** statt eines Gesamtdiagramms: Ein Diagramm mit
  13 Klassen und allen Value Objects wäre nicht mehr lesbar. Konzepte aus anderen Kontexten
  erscheinen ohne Attribute mit Stereotyp `<<Bestand>>` bzw. `<<extern>>`.
- **Technisch weggelassen:** alle `id`, `RentalUnit::version` (Sperre gegen gleichzeitiges
  Bearbeiten), `showInList`, `cssClass`, `lastModified`, `lastModifiedBy`. `Tenancy::noticeGiven`
  ist nicht technisch, wird aber wegen REGEL-15 bewusst nicht übernommen.
  Verworfen: `lastModifiedBy` als „Bearbeiter“ zu übernehmen – keine Regel nutzt es.
- **Identität ohne Fachnummer:** Mieter, Mietinteressent, Dienstleister und Besichtigung haben nur
  eine technische Kennung. Sie bleiben Entities mit einer vom System vergebenen Identität.
  Verworfen: eine Mieternummer erfinden – dafür gibt es im Altsystem keinen Beleg (F-18).
- **Mieteinheit:** Die Einheitsnummer („WE 03 links“) ist nur im Gebäude eindeutig. Identität =
  Gebäude + Einheitsnummer (Annahme, F-16).
- **Value Objects:** Geldbetrag, Fläche, Zeitraum, Anschrift, Kontaktdaten, Personenname,
  Zählerstand, Statuswechsel.
  - *Geldbetrag* mit Währung, obwohl das Altsystem keine speichert (Anzeige immer €) → Annahme Euro (F-20).
  - *Anschrift* einheitlich strukturiert, obwohl nur die Liegenschaft getrennte Felder hat. Sonst
    müsste die Übernahme der Anschrift ins Gebäude (REGEL-28) Text zerlegen.
  - *Zählerstand* strukturiert und *Mängel* als Liste statt Freitext, damit Einzug und Auszug
    vergleichbar sind und REGEL-21 („wenn Mängel festgestellt wurden“) prüfbar wird (F-19).
  - *Statuswechsel* ist ein Value Object, obwohl `IssueStatusChange` eine eigene Tabelle mit
    Kennung hat: Ein Eintrag wird nie geändert, nur angefügt und gelesen.
- **Übergabe als Entity, nicht als Value Object:** Je Mietverhältnis gibt es höchstens einen
  Einzug und einen Auszug; man spricht von „der Auszugsübergabe von MV-01003“. Lokale Identität ist
  die Übergabeart. Multiplizität 0..2, obwohl das Altsystem beliebig viele Übergaben zulässt
  (Befund für Schritt 6/7). Verworfen: Value Object – zwei Protokolle mit gleichen Werten an
  verschiedenen Tagen sind fachlich nicht austauschbar.
- **Kündigungsfrist als Attribut** des Mietverhältnisses (Vorgabe 3 Monate), statt fest im Ablauf
  wie im Altsystem (`+3 months` im Controller). So lässt sich F-10 ohne Modelländerung beantworten.
- **Eigentümer und Dienstleister:** Status aktiv/inaktiv als Wahrheitswert *aktiv*, weil es nur zwei
  Werte und keinen Ablauf gibt. Bei der Liegenschaft heißt der Status *Verwaltungsstatus* (aktiv/beendet).
- **Erfassungszeitpunkt der Meldung** ist fachlich (Reihenfolge, Bearbeitungsdauer), kein
  technischer Zeitstempel.
- **Benutzer** wird nur als Verweis modelliert (Bearbeiter, Mitarbeiter, „geändert von“). Passwort und
  Rollenliste sind technisch, die Zugangsverwaltung ist kein Pflichtbereich.

### Glossar und Regelkatalog angeglichen

- Einheitenstatus heißt **frei** statt *verfügbar* (Oberfläche, REGEL-21, REGEL-29). REGEL-01 wurde
  entsprechend umformuliert.
- **Bearbeiter** gilt jetzt für Meldung *und* Aufgabe: „zuständig“ (`Issue::assignee`) und
  „verantwortlich“ (`Task::responsible`) bedeuten dasselbe.
- Neue Begriffe: Objektnummer, Gebäudenummer, Einheitsnummer, Eigentümernummer, Meldungsnummer,
  Aufgabennummer, Verwaltungsstatus, Besichtigungsstatus, Statuswechsel, Geldbetrag, Anschrift,
  Kontaktdaten; Übergabeart beim Eintrag Einzug/Auszug ergänzt.

---

## 2026-10-27 – Ü4: Schritt 6 (Zustände)

**KI-Einsatz:** Claude (Claude Code) hat alle Statusänderungen im Code gesucht (`setStatus(`,
Statusfelder in `src/Form`, Bedingungen in `templates`), in Ist-Tabellen mit Fundstellen übertragen
und daraus `verhalten.md` mit Befunden B-01 bis B-24 entworfen. Die Entscheidungen sind Vorschläge für
die Gruppe.

### Vorgehen

- Für jede Entity mit Lebenszyklus zwei Teile: **Ist** (alles, was das Altsystem zulässt, mit
  Fundstelle und Angabe, ob die Bedingung in der Fachlogik, nur in der Oberfläche oder gar nicht
  geprüft wird) und **Modell** (fachlich erlaubte Übergänge). Verworfen: nur das Modell zu zeigen –
  dann wäre nicht nachvollziehbar, welche Kanten Befunde des Altsystems sind.
- Erfasst: Mietverhältnis, Mieteinheit, Mietinteressent, Besichtigung, Meldung, Aufgabe,
  Liegenschaft. Nicht erfasst: Übergabe (kein Status), Eigentümer und Dienstleister (nur *aktiv*).
- Die **freie Statusänderung per Formular** (Mieteinheit, Mietinteressent, Aufgabe, Meldung) wurde je
  Entity als eine Zeile „jeder → jeder“ erfasst und nicht ins Modell übernommen (B-07, B-11, B-15, B-20).
- Mermaid: Zustände mit Umlaut oder Leerzeichen bekommen einen ASCII-Alias (`state "gekündigt" as
  gekuendigt`), wie im Beispiel der Übung.

### Wichtigste Entscheidungen

- **B-01 aktiv → beendet** (Kante aus dem Übungsbeispiel): nur bei befristetem Mietverhältnis
  zugelassen, sonst nicht. Verworfen: Kante ganz streichen – dann könnte ein befristetes
  Mietverhältnis nie ohne Kündigung enden (F-12).
- **B-04 Entwurf verwerfen** neu eingeführt: Im Altsystem gibt es keinen Weg aus dem Entwurf, die
  Mieteinheit bleibt dauerhaft reserviert. Dazu passend **Reservierung aufheben** an der Mieteinheit (B-08).
- **B-07 Einheitenstatus:** Reserviert, vermietet und gekündigt entstehen nur als Folge der
  Vermietung. Von Hand bleiben nur sperren, entsperren und Renovierung abschließen.
- **Meldung:** *erledigt* und *abgelehnt* werden eigene Aktionen (B-17). Schließen bleibt aus
  „in Bearbeitung“ möglich, damit AF-19 unverändert gilt; *erledigt* ist ein optionaler
  Zwischenschritt (F-22). Jeder Übergang schreibt einen Statuswechsel (B-16, REGEL-09).
- **Aufgabe:** Wiedereröffnen aus erledigt *und* abgebrochen, immer mit Begründung (B-21), und
  direkt nach *zugewiesen*, wenn ein Dienstleister eingetragen ist (B-22).
- **Besichtigung:** Im Altsystem bleibt jede Besichtigung „geplant“; durchführen und absagen sind
  neu (B-14).
- **Abhängige Übergänge** (z. B. Mietverhältnis aktivieren → Mieteinheit vermietet) stehen in einer
  eigenen Tabelle als Vorarbeit für die Aggregatgrenzen in Schritt 8.
- Glossar ergänzt: Reservierung, Verwerfen, Absage, Ablehnung.

---

## 2026-10-27 – Ü4: Schritt 7 (Regeln präzisieren)

**KI-Einsatz:** Claude (Claude Code) hat alle Regeln um Anwendungsfall, Vor- und Nachbedingung bzw.
Invariante, betroffene Objekte und Entscheidung ergänzt und aus den Befunden von Schritt 6 fünf neue
Regeln abgeleitet. Fundstellen und Gegenbeobachtungen aus Ü2/Ü3 sind unverändert übernommen.
Die Entscheidungen sind Vorschläge für die Gruppe.

### Format

- Jede Regel hat einen **Titel** in der Überschrift (wie im Leitfaden-Beispiel) und steht zusätzlich
  in einer **Übersichtstabelle** mit Art und Status.
- Neue Felder: *Anwendungsfall*, *Vorbedingung*, *Nachbedingung*, *Invariante*, *Betroffene Objekte*,
  *Entscheidung*. Das Feld *Status* beschreibt weiterhin das Altsystem; wie die Regel im Modell gilt,
  steht in *Entscheidung*. Verworfen: den Status nach der Entscheidung auf „bestätigt“ zu setzen –
  dann ginge verloren, wo das Altsystem widersprüchlich ist.
- *Betroffene Objekte* unterscheidet geänderte und nur gelesene Objekte, weil das für die
  Aggregatgrenzen in Schritt 8 entscheidend ist.
- Offene Fragen verweisen nur noch auf die F-Nummern im Logbuch, statt sie zu wiederholen. Fragen,
  die bisher nur im Regelkatalog standen, sind als F-23 bis F-28 neu in der Liste.
- Prüffrage „Könnte jemand die Regel umsetzen, ohne den PHP-Code zu lesen?“: Vor- und
  Nachbedingungen nennen konkrete Zustände, Werte und Formeln (z. B. Nummernformate ab `ME-02001`,
  Zuordnung Kategorie → Gewerk), keine Code-Namen.

### Entscheidungen bei Widersprüchen

| Regel | Entscheidung | Verworfen |
|---|---|---|
| REGEL-01 | Entwurf auch für reservierte Mieteinheit | nur frei (wie der Button) – dann wäre der Weg Zusage → Entwurf blockiert |
| REGEL-03 | Liegenschaft = Liegenschaft der Mieteinheit auch beim Bearbeiten; bei mehreren Mietern kein automatischer meldender Mieter | erster Mieter (Altsystem) – zufällig |
| REGEL-05 | nur beim Erfassen, nur *dringend* (vorläufig) | Angleichen an REGEL-08 – ohne Antwort auf F-04 nicht entscheidbar |
| REGEL-07 | jede Aufgabe zur Meldung, auch per „Neue Aufgabe“ und beim Wiedereröffnen | – |
| REGEL-10, 18, 27 | Nummern aus einer Folge statt aus der Anzahl; nicht änderbar | Nummer im Formular änderbar – sie ist die Identität |
| REGEL-12 | Entwürfe dürfen sich überschneiden, erneute Prüfung beim Aktivieren | Überschneidung auch für Entwürfe verbieten – widerspricht mehreren Interessenten (F-06) |
| REGEL-13 | Aufhebung frühestens zum Beginn | – |
| REGEL-14 | mindestens ein Mieter in allen Zuständen nach dem Entwurf | nur bei *aktiv* (Altsystem) |
| REGEL-17 | höchstens ein Einzug und ein Auszug | – |
| REGEL-19 | Kündigungsfrist als Attribut, Vorgabe 3 Monate | fester Wert im Ablauf |
| REGEL-20 | Rolle und Zustand in der Fachlogik prüfen; Entwurf wird verworfen statt aufgehoben | – |
| REGEL-21 | Auszug ohne Kündigung nur bei befristetem Mietverhältnis | – |
| REGEL-22 | Begründung auch bei abgebrochener Aufgabe; zurück nach *zugewiesen*, wenn ein Dienstleister eingetragen ist | – |
| REGEL-23, 24 | Vorschlag nur aktiver Dienstleister mit Ansprechpartner; bei mehreren der erste nach Firmenname (Annahme) | – |
| REGEL-25 | Vor- und Nachname werden bei der Zusage angegeben | automatische Trennung am Leerzeichen |
| REGEL-26 | inaktive Eigentümer nicht zuordenbar | – |
| REGEL-28 | Mieteinheit wechselt das Gebäude nicht | – |
| REGEL-29 | Einheitenstatus nur über Übergänge; Einhaltung über Ereignisse (Schritt 8) | – |

### Neue Regeln aus Schritt 6

- **REGEL-31** Entwurf verwerfen, Reservierung aufheben (B-04, B-08)
- **REGEL-32** Meldung nur mit Kommentar ablehnen (B-17)
- **REGEL-33** Aufgabe nur mit Begründung abbrechen (B-23)
- **REGEL-34** Zustände nur über erlaubte Übergänge (B-07, B-11, B-15, B-20) – eine Regel statt vier
  gleichlautender
- **REGEL-35** Verwaltung einer Liegenschaft nur ohne laufende Mietverhältnisse beenden (Annahme, F-21)

Neue Nummern statt Einordnung in bestehende Regeln, damit sich REGEL-01 bis REGEL-30 nicht verschieben.

---

## 2026-10-27 – Ü4: Schritt 8 (Aggregate) und Abschlusscheck

**KI-Einsatz:** Claude (Claude Code) hat aus den *Betroffenen Objekten* der Regeln und den
*Abhängigen Übergängen* in `verhalten.md` die Aggregate geschnitten, den Abschnitt *Aggregate* in
`fachmodell.md` geschrieben und jeder Regel ihr Aggregat zugeordnet. Die Prüfungen unten liefen
per Skript über `model/`. Die Entscheidungen sind Vorschläge für die Gruppe.

### Ergebnis

Neun Aggregate: Liegenschaft (mit Gebäuden), Mieteinheit, Eigentümer, Mieter, Dienstleister
(Bestand); Mietinteressent (mit Besichtigungen), Mietverhältnis (mit Übergaben) (Vermietung);
Meldung (mit Statushistorie), Aufgabe (Meldungen und Aufgaben). Dazu kommen Tabellen für Verweise
über die Identität, Ereignisse, Regeln über mehrere Aggregate und die Prüffrage.

### Entscheidungen

- **Aufgabe nicht in der Meldung.** Das war die schwierigste Grenze: REGEL-02, 07, 22 und 32
  betreffen beide. Entscheidend war, dass es Aufgaben ohne Meldung gibt (AF-21). Verworfen:
  Aufgabe als Teil der Meldung; ein Zähler offener Aufgaben in der Meldung (Doppelung wie
  `noticeGiven`). Stattdessen prüft die Anwendungsschicht, und REGEL-02/REGEL-22 laufen für dieselbe
  Meldung nacheinander.
- **Mieteinheit nicht in der Liegenschaft**, weil sich ihr Einheitenstatus laufend ändert und sie
  von vier anderen Aggregaten angesprochen wird. Der Verweis Mieteinheit → Gebäude zeigt auf ein
  Objekt innerhalb der Liegenschaft. Zugelassen, weil die Gebäudenummer die Objektnummer enthält und
  sich nie ändert. Verworfen: Gebäude als eigenes Aggregat (Nummernvergabe über zwei Aggregate).
- **Besichtigung im Mietinteressenten**, weil eine Planung den Interessentenstatus ändert und nur
  vor Zusage oder Absage erlaubt ist.
- **Statushistorie in der Meldung**: Damit ist B-16 (Übergang ohne Eintrag) durch die Struktur
  ausgeschlossen, nicht nur durch eine Regel.
- **Einheitenstatus über Ereignisse** (REGEL-29), wie im Beispiel der Übung (Mieteinheit reagiert auf
  »gekündigt«). Annahme: Die Reaktion läuft in derselben Aktion. Ob sie zeitversetzt laufen darf, wird
  beim Entwurf der Zielarchitektur entschieden.
- **Nummernvergabe** als fachlicher Dienst mit je einer Folge (REGEL-10, 18, 27). Die Gebäudenummer
  vergibt die Liegenschaft selbst.
- **REGEL-10, 27, 28, 31** betreffen je zwei Aggregate, fassen aber zwei gleichartige Aussagen
  zusammen. Jede Aussage gehört zu genau einem Aggregat. Verworfen: die Regeln teilen – das hätte die
  Nummern verschoben.
- Regelkatalog: neues Feld *Aggregat* je Regel und Spalte *Aggregat* in der Übersicht.

### Begriffe angeglichen

- „Rückgabe“ → **Auszug** (Glossar: Einzug/Auszug statt Übergabe/Rückgabe): *Auszugstermin* statt
  *Rückgabetermin* in fachmodell.md, verhalten.md, regelkatalog.md; AF-07 und AF-08 in
  anwendungsfaelle.md. Die Button-Beschriftung `Übergabe / Rückgabe` bleibt als Oberflächentext.
- „Einheit“ und „Interessent“ → **Mieteinheit**, **Mietinteressent** in verhalten.md.

### Abschlusscheck (Leitfaden, Projekttermin P1)

| Punkt | Stand | Prüfung |
|---|---|---|
| Ein anderes Team könnte aus `model/` das System bauen, ohne den PHP-Code zu lesen. | weitgehend | Bedingungen nennen Zustände, Werte und Formeln; Code-Namen nur in Fundstellen. Offen: die Annahmen hängen an F-Fragen, die der Fachbereich beantworten muss. |
| Jede Regel hat Fundstelle, Bedingungen, Aggregat und – bei Widerspruch – eine Entscheidung. | ja | Skript: alle 34 Regeln (ohne die zusammengeführte REGEL-06) haben Anwendungsfall, Vor-/Nachbedingung oder Invariante, betroffene Objekte, Aggregat, Fundstellen und Entscheidung. REGEL-31 bis 35 haben als Fundstelle die Stelle, an der sie im Altsystem fehlen. |
| Glossar, Anwendungsfälle, Kontexte, Fachmodell, Verhalten und Regeln verwenden dieselben Begriffe. | ja | Skript-Suche nach Synonymen außerhalb von Code und Zitaten (verfügbar, Mietvertrag, Objekt, Immobilie, Schadensmeldung, Einheit, Interessent, Rückgabe); Treffer korrigiert. Übrig sind nur Oberflächentexte und der Termintyp „Handwerker“ (Kontext Termine). |
| Offene Fragen stehen im Logbuch. | ja | F-01 bis F-28; jede im Modell genannte F-Nummer gibt es in der Liste. |

**Noch offen vor der Abgabe:**

- PDF-Export (`fachmodell.pdf`, `verhalten.pdf`, `regelkatalog.pdf`): Werkzeug klären.
- Diagramme in einer Mermaid-Vorschau prüfen; hier konnten sie nicht gerendert werden.
- Die Gruppe prüft die als Vorschlag gekennzeichneten Entscheidungen aus Schritt 5 bis 8.
- Begründungen aus den frühen Einträgen (*nachzutragen*) ergänzen.

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
| F-18 | Brauchen Mieter, Mietinteressenten und Dienstleister eine fachliche Nummer (z. B. für Schriftverkehr)? | – (Fachmodell) |
| F-19 | Sollen Zählerstände und Mängel bei der Übergabe einzeln erfasst werden statt als Freitext? | REGEL-17, REGEL-21 |
| F-20 | Werden alle Beträge in Euro geführt? | REGEL-16 |
| F-21 | Darf die Verwaltung einer Liegenschaft beendet werden, solange dort Mietverhältnisse laufen? | – (verhalten.md) |
| F-22 | Was unterscheidet eine erledigte von einer geschlossenen Meldung (z. B. Rückmeldung des Mieters)? | REGEL-02 |
| F-23 | Darf eine Mieteinheit während der Kündigungsfrist schon nachvermietet werden (Entwurf trotz Einheitenstatus „gekündigt“)? | REGEL-01 |
| F-24 | Wer darf einer Meldung einen anderen Bearbeiter zuweisen? | REGEL-04 |
| F-25 | Sind Meldungs- und Vertragsnummer für Mieter sichtbar? | REGEL-10, REGEL-18 |
| F-26 | Ist der Ansprechpartner bei Dienstleister-Aufgaben Pflicht, oder genügt der Dienstleister? | REGEL-23 |
| F-27 | Nach welchen Kriterien wird unter mehreren passenden Dienstleistern ausgewählt? | REGEL-24 |
| F-28 | Sind Objekt- und Eigentümernummern frei vergebbar oder fest vom System vergeben? | REGEL-27 |
