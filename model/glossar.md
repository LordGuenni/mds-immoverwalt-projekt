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
| Mieteinheit | Einheit, Wohnung, Gewerbe | `RentalUnit` | `rental_unit` | Vermietbare Einheit, zum Beispiel Wohnung, Gewerbeeinheit oder Stellplatz. | Umfasst alle unterstützten Einheitstypen und ist im Fachmodell etabliert. |
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
| Einheitenstatus | Status | `RentalUnit::$status` | `rental_unit.status` | Belegungs- oder Betriebszustand einer Mieteinheit, zum Beispiel verfügbar, reserviert oder vermietet. | Trennt den Zustand der Einheit vom Meldungsstatus. |
| Vertragsnummer | Vertragsnummer | `Tenancy::$contractNumber` | `tenancy.contract_number` | Eindeutige Kennung eines Mietverhältnisses. | „Vertragsnummer“ bleibt als Feldname fachlich verständlich, obwohl der Oberbegriff Mietverhältnis lautet. |

## Schreibregeln

- In fachlichen Texten und Modellen werden die Begriffe aus **Begriff
  (gewählt)** verwendet.
- `Property`, `RentalUnit`, `Tenancy` usw. erscheinen nur als Code-Namen oder
  in Fundstellen.
- „Mietvertrag“ darf als wörtlicher Oberflächentext dokumentiert werden; im
  fachlichen Zusammenhang ist dafür **Mietverhältnis** zu schreiben.
- „Objekt“ und „Immobilie“ sind Oberflächensynonyme für **Liegenschaft**, nicht
  eigene Konzepte.
