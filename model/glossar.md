# Glossar ImmoVerwalt

Dieses Glossar definiert die Ubiquitous Language für das Fachmodell. Die
Spalten **Oberfläche**, **Code** und **Datenbank** dokumentieren vorhandene
Bezeichnungen; sie sind keine zusätzlichen fachlichen Begriffe. Wenn die
Oberfläche einen abweichenden Text verwendet, ist der Begriff in der Spalte
**gewählt** in fachlichen Modellen und Anforderungen zu verwenden.

| Begriff (gewählt) | Oberfläche | Code | Datenbank | Definition | Begründung der Wahl |
|---|---|---|---|---|---|
| Liegenschaft | Objekt, Immobilie | `Property` | `property` | Verwaltete Immobilie mit Adresse, Eigentümer und zugehörigen Gebäuden. | Fachlich präziser als das mehrdeutige „Objekt“; wird bereits in den Anwendungsfällen verwendet. |
| Gebäude | Gebäude | `Building` | `building` | Baulicher Bestandteil einer Liegenschaft, der Mieteinheiten zusammenfasst. | Entspricht dem fachlichen und technischen Sprachgebrauch. |
| Mieteinheit | Einheit, Wohnung, Gewerbe | `RentalUnit` | `rental_unit` | Vermietbare Einheit in einem Gebäude; Nutzungsart Wohnung oder Gewerbe. | Umfasst beide Nutzungsarten (`RentalUnit::type` = `WHG`/`GEW`) und ist im Fachmodell etabliert. |
| Mietverhältnis | Mietvertrag, Vertrag | `Tenancy` | `tenancy` | Vertragliche Beziehung zwischen einem oder mehreren Mietern und einer Mieteinheit über einen Zeitraum. | „Mietverhältnis“ bezeichnet die laufende Beziehung; „Mietvertrag“ bezeichnet eher das Dokument. |
| Mieter | Mieter | `Tenant` | `tenant` | Person, die als Vertragspartei einem Mietverhältnis zugeordnet ist. | Fachlich verständlich und in der Oberfläche bereits üblich. |
| Eigentümer | Eigentümer | `Owner` | `owner` | Person oder Organisation, der eine Liegenschaft gehört und für die abgerechnet wird. | Eindeutiger fachlicher Begriff; vermeidet die technische Bedeutung von „User“. |
| Mietinteressent | Interessent, Mietinteressent | `Prospect` | `prospect` | Person, die Interesse an einer Mieteinheit bekundet, aber noch kein Mieter ist. | Trennt die Vorvermietungsphase klar vom Mieter. |
| Besichtigung | Besichtigung | `Viewing` | `viewing` | Geplanter oder durchgeführter Termin, bei dem ein Mietinteressent eine Mieteinheit besichtigt. | Bezeichnet den fachlichen Vorgang, nicht nur einen Kalendereintrag. |
| Übergabe | Übergabe / Rückgabe | `Handover` | `handover` | Protokollierter Einzug oder Auszug mit Datum, Zählerständen, Schlüsseln und Mängeln. | „Übergabe“ ist der Oberbegriff für Einzug und Rückgabe. |
| Meldung | Meldung, Schadensmeldung | `Issue` | `issue` | Erfasster Mangel oder Schaden mit Zuordnung, Priorität, Status und Bearbeitungshistorie. | „Meldung“ umfasst auch nichttechnische Vorgänge und ist der konsistente Oberbegriff. |
| Aufgabe | Aufgabe, Handwerkeraufgabe | `Task` | `task` | Konkrete Arbeit, die einer Meldung oder einer Liegenschaft zugeordnet und bearbeitet wird. | „Aufgabe“ ist allgemeiner und nicht auf Handwerker beschränkt. |
| Dienstleister | Dienstleister, Handwerker | `ServiceProvider` | `service_provider` | Externer Betrieb oder Handwerker, der Aufgaben ausführt. | Schließt verschiedene Gewerke ein und entspricht dem Menübegriff. |
| Termin | Termin, Kalendertermin | `Appointment` | `appointment` | Kalendereintrag für eine fachliche Aktivität, optional mit Bezug zu einem Fachobjekt. | Gemeinsamer Begriff für Vor-Ort-, Besprechungs- und automatisch erzeugte Termine. |
| Sollstellung | Sollstellung, Forderung | `Charge` | `charge` | Für ein Mietverhältnis erzeugte Forderung für einen Abrechnungsmonat. | „Sollstellung“ bezeichnet den Buchungsvorgang; „Forderung“ ist nur die wirtschaftliche Wirkung. |
| Zahlung | Mietzahlung, Zahlungseingang | `Payment` | `payment` | Zahlungseingang, der einem Mietverhältnis und optional einer Sollstellung zugeordnet wird. | Eindeutiger Oberbegriff für manuell erfasste Zahlungseingänge. |
| Buchung | Buchung, Einnahme/Ausgabe | `Booking` | `booking` | Einnahme oder Ausgabe einer Liegenschaft mit Betrag, Kategorie und Zuordnung. | Deckt beide Buchungsrichtungen ab. |
| Eigentümerabrechnung | Eigentümerabrechnung | `AccountingService::ownerStatement` | *(keine eigene Tabelle)* | Abrechnung der Einnahmen, Ausgaben und Verwaltungsvergütung für einen Eigentümer. | Fachlicher Vorgang, der aus Buchungen und Stammdaten berechnet wird. |
| Leerstand | Leerstand, Leerstandsbericht | `PropertyService::getStatistics` | *(berechnetes Merkmal)* | Zustand einer Mieteinheit ohne aktives Mietverhältnis sowie die daraus berechnete Quote bzw. der Mietausfall. | Einheitlicher Begriff für Zustand und Berichtsauswertung. |
| Benutzer | Benutzer, Mitarbeiter | `User` | `app_user` | Person mit Zugang zur Verwaltungssoftware und einer oder mehreren Systemrollen. | „Benutzer“ ist der technische Datensatz; „Mitarbeiter“ beschreibt die typische Person dahinter. |
| Meldungsstatus | Status | `Issue::$status` | `issue.status` | Bearbeitungszustand einer Meldung, zum Beispiel neu, in Prüfung, in Bearbeitung oder geschlossen. | Präzisiert den sonst mehrdeutigen Begriff „Status“. |
| Einheitenstatus | Status | `RentalUnit::$status` | `rental_unit.status` | Belegungs- oder Betriebszustand einer Mieteinheit: frei, reserviert, vermietet, gekündigt, Renovierung, gesperrt. | Trennt den Zustand der Einheit vom Meldungsstatus. „frei“ statt „verfügbar“, wie in der Oberfläche. |
| Vertragsnummer | Vertragsnummer | `Tenancy::$contractNumber` | `tenancy.contract_number` | Eindeutige Kennung eines Mietverhältnisses. | „Vertragsnummer“ bleibt als Feldname fachlich verständlich, obwohl der Oberbegriff Mietverhältnis lautet. |
| Nutzungsart | Typ | `RentalUnit::$type` | `rental_unit.type` | Art der Nutzung einer Mieteinheit: Wohnung oder Gewerbe. | „Typ“ ist zu allgemein. |
| Angebotsmiete | Kaltmiete (bei der Einheit) | `RentalUnit::$coldRent` | `rental_unit.cold_rent` | Kaltmiete, zu der eine Mieteinheit angeboten wird; Vorschlag für neue Mietverhältnisse. | Gleicher Code-Name wie die vereinbarte Kaltmiete, aber andere Bedeutung – deshalb eigener Begriff (siehe Logbuch). |
| Kaltmiete | Kaltmiete | `Tenancy::$coldRent` | `tenancy.cold_rent` | Im Mietverhältnis vereinbarte monatliche Miete ohne Nebenkosten. | Gilt nur für das Mietverhältnis; ändert sich nicht, wenn die Angebotsmiete geändert wird. |
| Nebenkostenvorauszahlung | NK-Vorauszahlung | `serviceChargeAdvance` | `service_charge_advance` | Monatlicher Abschlag auf die Betriebskosten; bei der Mieteinheit als Vorschlag, im Mietverhältnis vereinbart. | Ausgeschriebener Fachbegriff statt Abkürzung. |
| Gesamtmiete | Gesamtmiete | `Tenancy::getTotalRent()` | *(berechnet)* | Kaltmiete plus Nebenkostenvorauszahlung. | Abgeleiteter Wert, wird nicht gespeichert. |
| Kaution | Kaution | `Tenancy::$deposit` | `tenancy.deposit` | Sicherheitsleistung des Mieters; ohne Angabe drei Kaltmieten. | Fachlich üblich. |
| Laufzeit | Vertragsbeginn, Vertragsende | `Tenancy::$startDate`, `$endDate` | `start_date`, `end_date` | Zeitraum eines Mietverhältnisses von Beginn bis Ende; ohne Ende unbefristet. | Ein Begriff für das zusammengehörige Paar Beginn/Ende. |
| Kündigung | Kündigung erfassen | `TenancyController::terminate()` | – | Ordentliche Beendigung eines aktiven Mietverhältnisses unter Einhaltung der Kündigungsfrist. | Abgegrenzt von der Aufhebung. |
| Kündigungsdatum | Kündigung vom | `Tenancy::$noticeDate` | `tenancy.notice_date` | Tag, an dem die Kündigung zugeht; Ausgangspunkt der Kündigungsfrist. | „Kündigung vom“ ist nur die Beschriftung. |
| Kündigungsfrist | – | fest `+3 months` in `TenancyController::terminate()` | – | Zeitraum zwischen Kündigungsdatum und Ende des Mietverhältnisses; im Altsystem immer drei Monate. | Begriff fehlt im Altsystem und wird für das Modell eingeführt (REGEL-19). |
| Aufhebung | Aufhebung / fristlos | `TenancyService::terminateImmediately()` | – | Außerordentliche Beendigung eines Mietverhältnisses zu einem Stichtag ohne Kündigungsfrist. | Oberflächenbegriff; deckt Aufhebungsvertrag und fristlose Kündigung ab. |
| Mietverhältnisstatus | Status | `Tenancy::$status` | `tenancy.status` | Zustand eines Mietverhältnisses: Entwurf, aktiv, gekündigt, aufgehoben, beendet. | Präzisiert „Status“; deutsche Zustandsnamen statt `DRAFT`, `ACTIVE`, `NOTICE_GIVEN`, `TERMINATED`, `ENDED`. |
| Einzug / Auszug | Übergabe (Einzug), Rückgabe (Auszug) | `Handover::MOVE_IN`, `MOVE_OUT` | `handover.type` | Die beiden Arten einer Übergabe (Übergabeart). | Kürzer und eindeutiger als Übergabe/Rückgabe. |
| Zählerstand | Zählerstände | `Handover::$meterReadings` | `handover.meter_readings` | Bei einer Übergabe abgelesene Zählerstände der Mieteinheit (im Altsystem Freitext). | Fachlich üblich. |
| Mangel | festgestellte Mängel | `Handover::$defects` | `handover.defects` | Bei einer Übergabe festgestellter Schaden der Mieteinheit; bei Auszug führt ein Mangel zur Renovierung. | Abgegrenzt von der Meldung, die einen gemeldeten Schaden während der Mietzeit beschreibt. |
| Zusage | Zusage | `RentalService::acceptProspect()` | – | Entscheidung, einem Mietinteressenten eine Mieteinheit zu vermieten. | Oberflächenbegriff. |
| Interessentenstatus | Status | `Prospect::$status` | `prospect.status` | Stand eines Mietinteressenten: neu, Besichtigung geplant, Bewerbung eingegangen, Zusage, Absage. | Präzisiert „Status“. |
| Priorität | Priorität | `Issue::$priority`, `Task::$priority` | `priority` | Dringlichkeit einer Meldung oder Aufgabe in vier Stufen: niedrig, normal, hoch, dringend. | Fachlich üblich. |
| Kategorie | Kategorie | `Issue::$category` | `issue.category` | Art einer Meldung, zum Beispiel Heizung, Sanitär, Elektrik, Schimmel, Beschwerde. | Abgegrenzt vom Gewerk des Dienstleisters. |
| Gewerk | Gewerk | `ServiceProvider::$trade` | `service_provider.trade` | Handwerklicher Fachbereich eines Dienstleisters, zum Beispiel Sanitär, Heizung, Elektro. | Fachbegriff im Bauwesen. |
| Ansprechpartner | Ansprechpartner | `contactPerson` | `contact_person` | Person, die bei einem Eigentümer oder Dienstleister zuständig ist. | Fachlich üblich. |
| Fälligkeit | fällig am | `Task::$dueDate` | `task.due_date` | Datum, bis zu dem eine Aufgabe erledigt sein soll. | „Frist“ ist für die Kündigungsfrist reserviert. |
| Aufgabenstatus | Status | `Task::$status` | `task.status` | Zustand einer Aufgabe: offen, zugewiesen, in Bearbeitung, wartet, erledigt, abgebrochen. | Präzisiert „Status“. |
| Statushistorie | Verlauf | `IssueStatusChange` | `issue_status_change` | Liste aller Statuswechsel einer Meldung mit altem und neuem Status, Zeitpunkt, Bearbeiter und Kommentar. | Beschreibt den Zweck statt des technischen Datensatzes. |
| Bearbeiter | zuständig, verantwortlich | `Issue::$assignee`, `Task::$responsible` | `issue.assignee_id`, `task.responsible_id` | Mitarbeiter, der für eine Meldung oder Aufgabe verantwortlich ist. | Abgegrenzt vom technischen Begriff „Benutzer“; „zuständig“ und „verantwortlich“ meinen dasselbe. |
| Objektnummer | Objektnummer | `Property::$propertyNumber` | `property.property_number` | Eindeutige Kennung einer Liegenschaft (`OB-0xxx`). | Oberflächenbegriff; „Objekt“ nur noch in diesem feststehenden Namen. |
| Gebäudenummer | Gebäudenummer | `Building::$buildingNumber` | `building.building_number` | Kennung eines Gebäudes aus Objektnummer und laufender Zahl (`OB-0xxx-G1`). | Oberflächenbegriff. |
| Einheitsnummer | Einheit | `RentalUnit::$unitNumber` | `rental_unit.unit_number` | Bezeichnung einer Mieteinheit innerhalb ihres Gebäudes, z. B. „WE 03 links“. | „Einheit“ allein ist mehrdeutig. |
| Eigentümernummer | Eigentümernummer | `Owner::$ownerNumber` | `owner.owner_number` | Eindeutige Kennung eines Eigentümers (`EI-xxxx`). | Oberflächenbegriff. |
| Meldungsnummer | Nr. | `Issue::$issueNumber` | `issue.issue_number` | Eindeutige, fortlaufende Kennung einer Meldung (`ME-0xxxx`). | Präzisiert „Nummer“. |
| Aufgabennummer | Nr. | `Task::$taskNumber` | `task.task_number` | Eindeutige, fortlaufende Kennung einer Aufgabe (`AU-0xxxx`). | Präzisiert „Nummer“. |
| Verwaltungsstatus | Status | `Property::$status` | `property.status` | Ob eine Liegenschaft verwaltet wird: aktiv oder beendet. | Präzisiert „Status“; Oberfläche „Verwaltung beendet“. |
| Besichtigungsstatus | Status | `Viewing::$status` | `viewing.status` | Stand einer Besichtigung: geplant, durchgeführt, abgesagt. | Präzisiert „Status“. |
| Statuswechsel | Verlauf (Zeile) | `IssueStatusChange` | `issue_status_change` | Ein Eintrag der Statushistorie: alter und neuer Meldungsstatus, Zeitpunkt, geändert von, Kommentar. | Benennt den einzelnen Eintrag, die Statushistorie ist die Liste. |
| Geldbetrag | (Betrag in €) | `decimal(10,2)` | `decimal` | Betrag mit Währung; Typ von Kaltmiete, Angebotsmiete, Nebenkostenvorauszahlung und Kaution. | Wertobjekt des Fachmodells; die Währung fehlt im Altsystem (Annahme Euro). |
| Anschrift | Anschrift, Adresse | `street`/`zip`/`city` bzw. `address` | verschieden | Postanschrift aus Straße, Hausnummer, Postleitzahl und Ort. | „Anschrift“ statt „Adresse“, wie in den meisten Formularen. |
| Kontaktdaten | E-Mail, Telefon | `email`, `phone` | `email`, `phone` | E-Mail-Adresse und Telefonnummer einer Person oder Firma. | Fasst die in vier Konzepten gleichen Felder zusammen. |
| Reservierung | reserviert | `RentalUnit::RESERVED` | `rental_unit.status` | Vormerkung einer Mieteinheit für einen Mietinteressenten oder einen Entwurf; die Einheit ist dann nicht frei. | Fachbegriff für den Einheitenstatus „reserviert“; wird im Modell aufgehoben, wenn der Entwurf verworfen wird. |
| Verwerfen | – | *(fehlt im Altsystem)* | – | Aufgeben eines Mietverhältnisses im Entwurf, bevor es aktiv wird; die Mieteinheit wird wieder frei. | Abgegrenzt von der Aufhebung, die nur aktive oder gekündigte Mietverhältnisse beendet (verhalten.md, B-04). |
| Absage | Absage | `Prospect::REJECTED` | `prospect.status` | Entscheidung, einem Mietinteressenten keine Mieteinheit zu vermieten. | Gegenstück zur Zusage. |
| Ablehnung | Abgelehnt | `Issue::REJECTED` | `issue.status` | Entscheidung, eine Meldung nicht zu bearbeiten; nur mit Kommentar. | Abgegrenzt von der Absage an einen Mietinteressenten. |
| Verwalter | verwalter | `ROLE_PROPERTY_MANAGER` | `app_user.roles` | Mitarbeiterrolle für Bestand, Meldungen, Aufgaben und Abrechnung. | Rollenname aus der Oberfläche. |
| Vermieter | vermieter | `ROLE_LETTING_MANAGER` | `app_user.roles` | Mitarbeiterrolle für Interessenten, Besichtigungen und neue Mietverhältnisse. | Rollenname aus der Oberfläche; gemeint ist ein Mitarbeiter der Verwaltung, nicht der Eigentümer. |
| Verwaltungshonorar | Verwaltungshonorar | `app.management_fee_percent` | *(Konfiguration)* | Vergütung der Hausverwaltung; pauschal 5 % der Mieteinnahmen, wenn nicht als Buchung erfasst. | Nur Abrechnung (optional). |

## Schreibregeln

- In fachlichen Texten und Modellen werden die Begriffe aus **Begriff
  (gewählt)** verwendet.
- `Property`, `RentalUnit`, `Tenancy` usw. erscheinen nur als Code-Namen oder
  in Fundstellen.
- „Mietvertrag“ darf als wörtlicher Oberflächentext dokumentiert werden; im
  fachlichen Zusammenhang ist dafür **Mietverhältnis** zu schreiben.
- „Objekt“ und „Immobilie“ sind Oberflächensynonyme für **Liegenschaft**, nicht
  eigene Konzepte.
- „Status“ wird nie allein verwendet, sondern immer als Einheitenstatus,
  Mietverhältnisstatus, Meldungsstatus, Aufgabenstatus oder Interessentenstatus.
- Zustände werden im Modell mit den deutschen Namen aus der Definition geschrieben
  (z. B. *gekündigt*), die Code-Werte (`NOTICE_GIVEN`) nur in Fundstellen.
- **Kaltmiete** meint immer die im Mietverhältnis vereinbarte Miete, **Angebotsmiete**
  die Miete der Mieteinheit.
