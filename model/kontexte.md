# Fachliche Kontexte ImmoVerwalt

Die Kontexte schneiden das System entlang der fachlichen Verantwortungen.
Begriffe aus dem [Glossar](./glossar.md) behalten innerhalb eines Kontextes
ihre dort beschriebene Bedeutung. Ein Datenaustausch zwischen Kontexten wird
als Nutzung eines veröffentlichten Modells beschrieben; die technischen
Entitäts- und Tabellennamen sind keine eigene Ubiquitous Language.

## Kontextübersicht

Die Zuordnung folgt der Spalte **Bereich** in
[anwendungsfaelle.md](./anwendungsfaelle.md). Jeder Anwendungsfall erscheint
genau einmal in der folgenden Tabelle.

| Kontext | Zweck und Anwendungsfälle | Zentrale Begriffe |
|---|---|---|
| **Vermietung** | Interessenten aufnehmen, Besichtigungen planen, Mietverhältnisse vorbereiten, Vertragsparteien verwalten, Mietverhältnisse aktivieren/kündigen und Übergaben protokollieren (AF-01 bis AF-09). | Mietinteressent, Besichtigung, Mieteinheit, Mietverhältnis, Mieter, Übergabe, Termin |
| **Bestand** | Liegenschaften, Gebäude, Mieteinheiten, Eigentümer, Mieter und Dienstleister als Stammdaten pflegen (AF-10 bis AF-15). | Liegenschaft, Gebäude, Mieteinheit, Eigentümer, Mieter, Dienstleister |
| **Meldungen und Aufgaben** | Meldungen erfassen, priorisieren und historisieren; Aufgaben aus Meldungen ableiten, Dienstleister beauftragen und Meldungen schließen (AF-16 bis AF-23). | Meldung, Aufgabe, Dienstleister, Mieteinheit, Liegenschaft, Mieter, Benutzer |
| **Termine** | Fachliche Aktivitäten im Kalender planen und ändern (AF-24). | Termin, Besichtigung, Übergabe, Meldung, Aufgabe |
| **Abrechnung** | Sollstellungen erzeugen, Zahlungen zuordnen, Liegenschaftsbuchungen erfassen und Eigentümerabrechnungen erstellen (AF-25 bis AF-28). | Mietverhältnis, Sollstellung, Zahlung, Buchung, Eigentümer, Eigentümerabrechnung |
| **Berichte** | Leerstand und entgangene Einnahmen aus den führenden Kontexten auswerten (AF-29). | Leerstand, Liegenschaft, Gebäude, Mieteinheit, Mietverhältnis |

## Zuordnung mit Datenabhängigkeiten

Die folgende Tabelle verwendet die geforderte Struktur. Die Spalte
**Anwendungsfälle** enthält alle fachlichen Anwendungsfälle des jeweiligen
Kontexts; die Spalte **nutzt Daten von** beschreibt die Datenabhängigkeit
zwischen den Kontexten.

| Kontext | Zentrale Begriffe | Anwendungsfälle | nutzt Daten von |
|---|---|---|---|
| **Vermietung** | Mietinteressent, Besichtigung, Mieteinheit, Mietverhältnis, Mieter, Übergabe | AF-01 bis AF-09 | Bestand (Liegenschaft, Gebäude, Mieteinheit, Mieter) und Termine (Kalenderdaten) |
| **Bestand** | Liegenschaft, Gebäude, Mieteinheit, Eigentümer, Mieter, Dienstleister | AF-10 bis AF-15 | keine; der Kontext ist für diese Stammdaten führend |
| **Meldungen und Aufgaben** | Meldung, Aufgabe, Dienstleister, Mieteinheit, Liegenschaft, Mieter, Benutzer | AF-16 bis AF-23 | Bestand (Liegenschaft, Mieteinheit, Mieter, Dienstleister) und Vermietung (aktives Mietverhältnis) |
| **Termine** | Termin, Besichtigung, Übergabe, Meldung, Aufgabe | AF-24 | Vermietung (Besichtigung, Übergabe) und Meldungen und Aufgaben (Meldung, Aufgabe) |
| **Abrechnung** | Mietverhältnis, Sollstellung, Zahlung, Buchung, Eigentümer, Eigentümerabrechnung | AF-25 bis AF-28 | Vermietung (aktiviertes Mietverhältnis, Laufzeit, Mieter, Mietbeträge) und Bestand (Eigentümer, Liegenschaft) |
| **Berichte** | Leerstand, Liegenschaft, Gebäude, Mieteinheit, Mietverhältnis | AF-29 | Bestand (Liegenschaft, Gebäude, Mieteinheit, Einheitenstatus) und Vermietung (aktive Mietverhältnisse, Mietansätze) |

## Vollständige Anwendungsfall-Zuordnung

Jeder der 29 Anwendungsfälle ist genau einem Kontext zugeordnet:

| Kontext | Zugeordnete Anwendungsfälle |
|---|---|
| **Vermietung** | AF-01 Mietinteressent erfassen; AF-02 Besichtigung terminieren; AF-03 Zusage erteilen und reservieren; AF-04 Mietvertragsentwurf anlegen; AF-05 Vertragsparteien verwalten; AF-06 Mietverhältnis aktivieren; AF-07 Mietverhältnis kündigen; AF-08 Mietverhältnis fristlos aufheben; AF-09 Wohnungsübergabe protokollieren |
| **Bestand** | AF-10 Liegenschaft anlegen und pflegen; AF-11 Gebäude strukturieren; AF-12 Mieteinheit anlegen und pflegen; AF-13 Eigentümer verwalten; AF-14 Mieterstammdaten pflegen; AF-15 Dienstleister pflegen |
| **Meldungen und Aufgaben** | AF-16 Schadensmeldung erfassen; AF-17 Meldungsstatus fortschreiben; AF-18 Handwerkeraufgabe beauftragen; AF-19 Meldung schließen; AF-20 Mietermitteilung drucken; AF-21 Aufgabe planen und steuern; AF-22 Aufgabe abschließen; AF-23 Aufgabe wiedereröffnen |
| **Termine** | AF-24 Vor-Ort-Termin vereinbaren |
| **Abrechnung** | AF-25 Monatliche Sollstellungen erzeugen; AF-26 Mietzahlung verbuchen; AF-27 Liegenschaftsbuchung erfassen; AF-28 Eigentümerabrechnung erstellen |
| **Berichte** | AF-29 Leerstand überwachen |

## Kontextgrenzen und Bedeutungsunterschiede

### Vermietung und Bestand

* **Mieteinheit** ist im Bestand ein Stammdatensatz mit baulicher Lage,
  Fläche, Nutzungsart und Einheitenstatus.
* In der Vermietung ist dieselbe Mieteinheit ein vermietbares Angebot, dessen
  Verfügbarkeit durch Reservierung, Mietverhältnis und Übergabe verändert
  wird.
* **Mieter** wird im Bestand als Person mit Stammdaten gepflegt. In der
  Vermietung ist der Mieter zusätzlich eine Vertragspartei eines konkreten
  Mietverhältnisses. Die Vertragszuordnung gehört zur Vermietung, die
  Personendaten gehören zum Bestand.

### Vermietung und Abrechnung

* Die Vermietung ist führend für Laufzeit, Status und Vertragsparteien eines
  **Mietverhältnisses**.
* Die Abrechnung nutzt ein aktiviertes Mietverhältnis mit den darin
  vereinbarten Beträgen. Sie verändert nicht die Vertragskonditionen,
  sondern erzeugt daraus **Sollstellungen** und ordnet **Zahlungen** zu.
* **Mieter** bezeichnet in der Abrechnung den Kontoinhaber des
  Mietverhältnisses; bei mehreren Mietern bleibt das Mietverhältnis die
  abrechnungsrelevante Einheit.

### Bestand und Meldungen/Aufgaben

* Bestand liefert die identifizierenden Stammdaten von Liegenschaft, Gebäude,
  Mieteinheit, Mieter und Dienstleister.
* Eine **Meldung** beschreibt im Vorgangskontext einen konkreten Mangel oder
  Schaden. Sie ist keine Änderung der Stammdaten.
* Eine **Aufgabe** ist ein Arbeitsauftrag zur Bearbeitung der Meldung. Der
  Dienstleister wird aus den Stammdaten ausgewählt; Status und Frist gehören
  zur Aufgabe und nicht zum Dienstleister.

### Meldungen/Aufgaben und Termine

* Ein **Termin** ist im Terminkontext ein Kalendereintrag.
* Eine **Besichtigung** bleibt fachlich ein Vermietungsvorgang und eine
  **Übergabe** ein Vermietungsvorgang; beide können zusätzlich einen Termin
  erzeugen.
* Ein Termin kann auf Meldung oder Aufgabe verweisen, übernimmt aber nicht
  deren Status. Änderungen am Termin ändern daher weder Meldungs- noch
  Aufgabenstatus automatisch.

### Abrechnung und Berichte

* Abrechnung ist führend für Sollstellungen, Zahlungen und Buchungen.
* Der Berichtskontext besitzt keine eigenen abrechnungsrelevanten
  Stammdaten. **Leerstand** wird aus Einheitenstatus und aktiven
  Mietverhältnissen berechnet; Mietausfall wird aus den Mietansätzen der
  betroffenen Mieteinheiten abgeleitet.
* Die **Eigentümerabrechnung** ist ein berechnetes Ergebnis aus Buchungen,
  Mietverhältnissen und Verwaltungsdaten und kein eigener Stammdatensatz.

## Datenflüsse zwischen Kontexten

| Von | Nach | Verwendete Daten |
|---|---|---|
| Bestand | Vermietung | Mieteinheit, Liegenschaft, Mieter und deren Stammdaten |
| Vermietung | Bestand | Änderungen am Einheitenstatus sowie Zuordnung von Mieteinheiten zu Mietverhältnissen |
| Vermietung | Abrechnung | Aktiviertes Mietverhältnis, Laufzeit, Mieter und vereinbarte Miet- und Vorauszahlungsbeträge |
| Bestand | Meldungen/Aufgaben | Liegenschaft, Mieteinheit, Mieter, Dienstleister |
| Meldungen/Aufgaben | Termine | Anlass und optionaler Bezug zu Meldung oder Aufgabe |
| Vermietung | Termine | Besichtigungs- und Übergabeanlass sowie Teilnehmer und Zeit |
| Abrechnung | Berichte | Buchungen, Mietansätze und Zahlungs-/Forderungsdaten für Auswertungen |
| Bestand und Vermietung | Berichte | Einheitenstatus, aktive Mietverhältnisse und Liegenschaftsstruktur |

## Verantwortlichkeiten

* **Vermietung** besitzt den Lebenszyklus des Mietverhältnisses und die
  fachliche Belegung einer Mieteinheit.
* **Bestand** besitzt die Stammdaten und die Hierarchie
  Liegenschaft → Gebäude → Mieteinheit.
* **Meldungen und Aufgaben** besitzen Bearbeitungsstatus,
  Statushistorie, Prioritäten und Arbeitsaufträge.
* **Termine** besitzt Kalenderdaten, nicht die fachlichen Lebenszyklen der
  referenzierten Objekte.
* **Abrechnung** besitzt finanzielle Vorgänge und berechnet die
  Eigentümerabrechnung.
* **Berichte** liest veröffentlichte Daten und ist für keine Stammdaten
  führend.
