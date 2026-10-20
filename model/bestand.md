# Bestandsaufnahme Altsystem (ImmoVerwalt)

Dieses Dokument erfasst alle Routen und Entitäten des Altsystems ImmoVerwalt (PHP 7.4 / Symfony 4.4 / Doctrine ORM). Es dient als methodische Grundlage für die Rekonstruktion des Fachmodells und der fachlichen Geschäftsvorfälle.

---

## 1. Mapped Entities (Doctrine ORM)

Ausgabe von `bin/console doctrine:mapping:info`: **18 gemappte Entitäten**

| Entität | Fachliche Beschreibung / Rolle im System | Quellcode |
|---|---|---|
| `App\Entity\Appointment` | Termin im Kalender (Besichtigung, Wohnungsübergabe, Handwerkertermin) mit Referenz auf Fachobjekt (`refType`, `refId`). | `src/Entity/Appointment.php` |
| `App\Entity\Booking` | Finanzbuchung (Einnahme oder Ausgabe) einer Liegenschaft mit Betrag, Kategorie, Buchungsdatum und Eigentümerzuordnung. | `src/Entity/Booking.php` |
| `App\Entity\Building` | Gebäude als baulicher Bestandteil einer Liegenschaft; fasst Mieteinheiten zusammen. | `src/Entity/Building.php` |
| `App\Entity\Charge` | Monatliche Sollstellung / Forderung an den Mieter (Kaltmiete, Nebenkostenvorauszahlung) mit Fälligkeit und Abrechnungsmonat. | `src/Entity/Charge.php` |
| `App\Entity\Handover` | Übergabe- bzw. Rückgabeprotokoll einer Mieteinheit (Einzug oder Auszug) mit Zählerständen und Mängeldokumentation. | `src/Entity/Handover.php` |
| `App\Entity\Issue` | Mängel- oder Schadensmeldung zu einer Liegenschaft bzw. Mieteinheit mit Status, Priorität und Kategorie/Gewerk. | `src/Entity/Issue.php` |
| `App\Entity\IssueStatusChange` | Historisierter Statustracker einer Meldung mit vorherigem und neuem Status, Bearbeiter, Zeitstempel und Begründung. | `src/Entity/IssueStatusChange.php` |
| `App\Entity\Owner` | Immobilieneigentümer (Auftraggeber der Verwaltung) mit Kontaktdaten, Steuernummer und Bankverbindung. | `src/Entity/Owner.php` |
| `App\Entity\Payment` | Zahlungseingang eines Mieters zu einem Mietverhältnis mit Betrag, Zahlungsdatum und Verwendungszweck. | `src/Entity/Payment.php` |
| `App\Entity\Property` | Liegenschaft / Immobilienobjekt im Bestand; zentrale Klammer für Gebäude, Eigentümer und Abrechnungen. | `src/Entity/Property.php` |
| `App\Entity\Prospect` | Mietinteressent für eine Mieteinheit mit Kontaktdaten, Wunschkriterien und Prozessstatus. | `src/Entity/Prospect.php` |
| `App\Entity\RentalUnit` | Mieteinheit (Wohnung, Gewerbeeinheit, Stellplatz) mit Flächenangabe, Zimmeranzahl, Sollmiete und Zustand/Status. | `src/Entity/RentalUnit.php` |
| `App\Entity\ServiceProvider` | Externer Dienstleister bzw. Handwerksbetrieb mit Gewerk (Sanitär, Heizung, Elektro etc.) und Ansprechpartner. | `src/Entity/ServiceProvider.php` |
| `App\Entity\Task` | Konkrete Arbeitsaufgabe (z. B. Reparaturauftrag aus einer Meldung) mit Fälligkeit, Dienstleisterzuweisung und Kosten. | `src/Entity/Task.php` |
| `App\Entity\Tenancy` | Mietverhältnis / Mietvertrag zwischen Eigentümer/Verwaltung und Mieter(n) über eine Mieteinheit. | `src/Entity/Tenancy.php` |
| `App\Entity\Tenant` | Mieter (Vertragspartei) mit Personendaten, Kontaktdaten und Verknüpfung zu Mietverträgen. | `src/Entity/Tenant.php` |
| `App\Entity\User` | Mitarbeiter / Benutzer der Verwaltungssoftware mit Login-Daten und Systemrollen. | `src/Entity/User.php` |
| `App\Entity\Viewing` | Besichtigungstermin zwischen Mietinteressent, zuständigem Mitarbeiter und Mieteinheit. | `src/Entity/Viewing.php` |

---

## 2. Routen nach Pflicht- und Fachbereichen

Jede Route stellt eine Aktion dar, die ein Benutzer oder das System auslösen kann.

### 2.1 Vermietung

| Route | Methode, URL | Controller | Was passiert fachlich? |
|---|---|---|---|
| `prospect_index` | ANY `/vermietung/interessenten` | `LettingController::prospects()` | Chronologische Übersicht aller eingegangenen Mietinteressenten anzeigen. |
| `prospect_new` | GET\|POST `/vermietung/interessenten/neu` | `LettingController::newProspect()` | Neuen Mietinteressenten mit Kontaktdaten und optionaler Wunsch-Mieteinheit erfassen. |
| `prospect_edit` | GET\|POST `/vermietung/interessenten/{id}/bearbeiten` | `LettingController::editProspect()` | Stammdaten, Wunschkriterien oder Notizen eines Mietinteressenten bearbeiten. |
| `prospect_accept` | POST `/vermietung/interessenten/{id}/zusage` | `LettingController::accept() → RentalService::acceptProspect()` | Zusage an Interessenten erteilen: wandelt Interessenten in Mieter-Stammsatz (`Tenant`) um, setzt Status auf `ACCEPTED`, reserviert die Mieteinheit (`RESERVED`) und leitet zur Vertragserstellung weiter. |
| `viewing_index` | ANY `/vermietung/besichtigungen` | `LettingController::viewings()` | Liste aller geplanten und durchgeführten Besichtigungstermine anzeigen. |
| `viewing_new` | GET\|POST `/vermietung/besichtigungen/neu` | `LettingController::newViewing() → RentalService::planViewing()` | Besichtigungstermin für einen Interessenten anlegen: setzt Interessentenstatus auf `VIEWING_PLANNED` und trägt synchron einen 30-minütigen Kalendertermin (`Appointment`) ein. |
| `tenancy_index` | ANY `/vermietung/mietverhaeltnisse` | `TenancyController::index()` | Übersicht aller Mietverhältnisse anzeigen; filterbar nach laufenden Verträgen (`current`), allen Verträgen oder nach Status. |
| `tenancy_new` | GET\|POST `/vermietung/mietverhaeltnisse/neu/{unit}` | `TenancyController::new() → TenancyService::createTenancy()` | Neuen Mietvertragsentwurf für eine freie oder reservierte Einheit anlegen: prüft Belegungsüberschneidungen, übernimmt Richtmieten, vergibt Vertragsnummer (`MV-xxxx`), setzt Status auf `DRAFT` und Einheit auf `RESERVED`. |
| `tenancy_show` | ANY `/vermietung/mietverhaeltnisse/{id}` | `TenancyController::show()` | Vertragsdetails, zugeordnete Mieter, Kontosaldo, Übergabeprotokolle und Termine des Mietverhältnisses einsehen. |
| `tenancy_activate` | POST `/vermietung/mietverhaeltnisse/{id}/aktivieren` | `TenancyController::activate() → TenancyService::activate()` | Entwurf eines Mietvertrags wird wirksam: prüft Vorhandensein von Mietern, setzt Vertrag auf `ACTIVE`, Mieteinheit auf `RENTED`, erzeugt automatischen Übergabetermin zum Einzug und generiert die erste monatliche Sollstellung. |
| `tenancy_tenant` | POST `/vermietung/mietverhaeltnisse/{id}/mieter` | `TenancyController::tenants() → TenancyService::addTenant() / removeTenant()` | Mieter zum Vertrag hinzufügen oder entfernen; verhindert das Entfernen des letzten Mieters bei einem aktiven Vertrag. |
| `tenancy_terminate` | POST `/tenancies/{id}/terminate` | `TenancyController::terminate()` | Ordentliche Kündigung eines aktiven Mietverhältnisses erfassen: setzt Kündigungsdatum, berechnet Vertragsende (+3 Monate), setzt Status von Vertrag und Einheit auf `NOTICE_GIVEN` und erzeugt Auszugs-Übergabetermin. |
| `tenancy_terminate_immediately` | POST `/vermietung/mietverhaeltnisse/{id}/aufheben` | `TenancyController::terminateImmediately() → TenancyService::terminateImmediately()` | Außerordentliche Kündigung / fristlose Aufhebung erfassen: beendet Vertrag sofort zum angegebenen Stichtag (`TERMINATED`), setzt Einheit auf `NOTICE_GIVEN` und legt Auszugs-Übergabetermin an. |
| `handover_new` | GET\|POST `/vermietung/mietverhaeltnisse/{id}/uebergabe` | `TenancyController::handover() → TenancyService::recordHandover()` | Übergabe (Einzug) oder Rückgabe (Auszug) mit Zählerständen und Mängeln protokollieren; bei Rückgabe wird Vertrag auf `ENDED` gesetzt und die Einheit je nach Mängeln auf `AVAILABLE` oder `RENOVATION` gesetzt. |

---

### 2.2 Bestand und Stammdaten

| Route | Methode, URL | Controller | Was passiert fachlich? |
|---|---|---|---|
| `property_index` | ANY `/stammdaten/objekte/` | `PropertyController::index()` | Liste aller verwalteten Liegenschaften / Immobilienobjekte anzeigen. |
| `property_new` | GET\|POST `/stammdaten/objekte/neu` | `PropertyController::new() → PropertyService::nextPropertyNumber()` | Neue Liegenschaft mit Adresse, Eigentümer und Verwaltergebühr erfassen; vergibt automatisch nächste Objektnummer (`OBJ-xxxx`). |
| `property_show` | ANY `/stammdaten/objekte/{id}` | `PropertyController::show() → PropertyService::getStatistics(), AppointmentService::findFor()` | Detailansicht einer Liegenschaft mit Gebäuden, Einheiten, Kennzahlen (Sollmiete, Leerstand) und anstehenden Liegenschaftsterminen anzeigen. |
| `property_edit` | GET\|POST `/stammdaten/objekte/{id}/bearbeiten` | `PropertyController::edit()` | Liegenschaftsdaten (Bezeichnung, Adresse, Baujahr, Eigentümer, Verwaltergebühr) bearbeiten. |
| `building_new` | GET\|POST `/stammdaten/objekte/{id}/gebaeude/neu` | `PropertyController::newBuilding()` | Neues Gebäude innerhalb einer Liegenschaft anlegen; vergibt Gebäudenummer (`OBJ-xxxx-Gy`) und übernimmt die Liegenschaftsadresse. |
| `building_edit` | GET\|POST `/stammdaten/objekte/gebaeude/{id}/bearbeiten` | `PropertyController::editBuilding()` | Gebäudedaten (Bezeichnung, Adresse, Baujahr, Etagen) aktualisieren. |
| `unit_index` | ANY `/stammdaten/einheiten/` | `RentalUnitController::index()` | Gesamtliste aller Mieteinheiten anzeigen, optional gefiltert nach Belegungsstatus (`AVAILABLE`, `RENTED`, etc.). |
| `unit_new` | GET\|POST `/stammdaten/einheiten/neu/{building}` | `RentalUnitController::new()` | Neue Mieteinheit (Wohnung, Gewerbe, Stellplatz) in einem Gebäude mit Fläche, Zimmern, Sollmiete und Vorauszahlung anlegen. |
| `unit_show` | ANY `/stammdaten/einheiten/{id}` | `RentalUnitController::show()` | Detailseite einer Einheit mit aktuellem Mietvertrag, Schadensmeldungen, Interessenten, Sollstellungen und Terminen anzeigen. |
| `unit_edit` | GET\|POST `/stammdaten/einheiten/{id}/bearbeiten` | `RentalUnitController::edit()` | Mieteinheitsdaten bearbeiten (Fläche, Zimmer, Kaltmiete, Status) und Benutzer der Änderung protokollieren (`lastModifiedBy`). |
| `owner_index` | ANY `/stammdaten/eigentuemer/` | `OwnerController::index()` | Liste aller Immobilieneigentümer alphabetisch anzeigen. |
| `owner_new` | GET\|POST `/stammdaten/eigentuemer/neu` | `OwnerController::new()` | Neuen Eigentümer anlegen mit automatischer Vergabe der Eigentümernummer (`EI-xxxx`). |
| `owner_show` | ANY `/stammdaten/eigentuemer/{id}` | `OwnerController::show()` | Stammdaten, Bankverbindung und Liste der zugeordneten Liegenschaften eines Eigentümers einsehen. |
| `owner_edit` | GET\|POST `/stammdaten/eigentuemer/{id}/bearbeiten` | `OwnerController::edit()` | Stammdaten, Adresse und Bankverbindung eines Eigentümers aktualisieren. |
| `tenant_index` | ANY `/stammdaten/mieter/` | `TenantController::index()` | Mieterverzeichnis anzeigen, filterbar nach Namenssuchbegriff (`q`). |
| `tenant_new` | GET\|POST `/stammdaten/mieter/neu` | `TenantController::new()` | Mieter-Datensatz manuell mit Personen- und Kontaktdaten (Name, Telefon, E-Mail, Geburtsdatum, Notizen) erfassen. |
| `tenant_show` | ANY `/stammdaten/mieter/{id}` | `TenantController::show()` | Kontaktdaten und Übersicht aller historischen und aktiven Mietverhältnisse eines Mieters anzeigen. |
| `tenant_edit` | GET\|POST `/stammdaten/mieter/{id}/bearbeiten` | `TenantController::edit()` | Stammdaten und Kontaktdaten eines Mieters bearbeiten. |
| `provider_index` | ANY `/stammdaten/dienstleister/` | `ServiceProviderController::index()` | Liste aller externen Handwerks- und Dienstleisterbetriebe anzeigen. |
| `provider_new` | GET\|POST `/stammdaten/dienstleister/neu` | `ServiceProviderController::new()` | Neuen Dienstleister erfassen (Firmenname, Gewerk z. B. Sanitär/Heizung, Ansprechpartner, Kontakt). |
| `provider_edit` | GET\|POST `/stammdaten/dienstleister/{id}/bearbeiten` | `ServiceProviderController::edit()` | Stammdaten, Gewerk oder Ansprechpartner eines Dienstleisters bearbeiten. |

---

### 2.3 Vorgänge (Meldungen, Aufgaben, Termine)

| Route | Methode, URL | Controller | Was passiert fachlich? |
|---|---|---|---|
| `issue_index` | ANY `/vorgaenge/meldungen/` | `IssueController::index()` | Liste aller Schadens- und Mängelmeldungen anzeigen (Filter nach offen, alle oder Einzelstatus). |
| `issue_new` | GET\|POST `/vorgaenge/meldungen/neu` | `IssueController::new() → IssueService::createIssue()` | Schadensmeldung erfassen: vergibt Nummer (`MEL-xxxx`), verknüpft automatisch Liegenschaft und Mieter (bei Einheitbezug), weist Erfasser als Bearbeiter zu, setzt Priorität >= 4 direkt auf `IN_REVIEW` und dokumentiert Ersteintrag in Statushistorie. |
| `issue_show` | ANY `/vorgaenge/meldungen/{id}` | `IssueController::show() → IssueService::getCurrentTenancy(), IssueService::getRelatedAppointments()` | Detailansicht der Meldung inklusive Mietverhältnis, zugeordneten Aufgaben, Terminen und lückenloser Statushistorie anzeigen. |
| `issue_edit` | GET\|POST `/vorgaenge/meldungen/{id}/bearbeiten` | `IssueController::edit()` | Stammdaten einer Meldung (Titel, Beschreibung, Priorität, Kategorie, Zuständigkeit) bearbeiten. |
| `issue_status` | POST `/vorgaenge/meldungen/{id}/status` | `IssueController::status()` | Status einer Meldung manuell umschalten; protokolliert Vorher-/Nachher-Status, Bearbeiter und Kommentar in `IssueStatusChange`. |
| `issue_close` | POST `/vorgaenge/meldungen/{id}/schliessen` | `IssueController::close() → IssueService::closeIssue()` | Meldung formal abschließen (`CLOSED`); schlägt fehl, sofern noch offene Aufgaben zur Meldung existieren. |
| `issue_bulk` | POST `/vorgaenge/meldungen/sammelaenderung` | `IssueController::bulk()` | Sammeloperation: Status mehrerer ausgewählter Meldungen in der Listenansicht gleichzeitig aktualisieren. |
| `issue_add_task` | POST `/vorgaenge/meldungen/{id}/aufgabe` | `IssueController::addTask() → IssueService::createTaskFromIssue()` | Handwerker-/Folgeaufgabe zu einer Meldung erstellen: weist automatisch passenden Dienstleister nach Gewerk zu, berechnet Frist anhand Priorität und setzt Meldungsstatus auf `IN_PROGRESS`. |
| `issue_print` | ANY `/vorgaenge/meldungen/{id}/druck` | `IssueController::print()` | Druckansicht einer Mitteilung/Bestätigung der Meldung für den Mieter aufrufen. |
| `task_index` | ANY `/vorgaenge/aufgaben/` | `TaskController::index()` | Aufgabenliste anzeigen (standardmäßig offene Aufgaben, optional alle sortiert nach Fälligkeit). |
| `task_new` | GET\|POST `/vorgaenge/aufgaben/neu` | `TaskController::new() → TaskService::save()` | Neue Arbeitsaufgabe anlegen; vergibt Aufgabennummer (`AUF-xxxx`), übernimmt Ansprechpartner bei Dienstleisterzuweisung und setzt Status auf `ASSIGNED`. |
| `task_edit` | GET\|POST `/vorgaenge/aufgaben/{id}/bearbeiten` | `TaskController::edit() → TaskService::save()` | Aufgabe bearbeiten (Titel, Fälligkeit, Priorität, Dienstleister, Schätz- und Ist-Kosten, Notizen). |
| `task_done` | POST `/vorgaenge/aufgaben/{id}/erledigt` | `TaskController::done()` | Aufgabe als abgeschlossen und erledigt markieren (`DONE`). |
| `task_reopen` | POST `/vorgaenge/aufgaben/{id}/wiedereroeffnen` | `TaskController::reopen()` | Erledigte Aufgabe mit zwingend erforderlicher Begründung wieder öffnen (`OPEN`); protokolliert Datum und Grund in den Notizen. |
| `appointment_index` | ANY `/vorgaenge/termine/` | `AppointmentController::index()` | Kalenderansicht aufrufen (anstehende Termine oder alle chronologisch absteigend). |
| `appointment_new` | GET\|POST `/vorgaenge/termine/neu` | `AppointmentController::new()` | Neuen Termin (z. B. Ortstermin, Vor-Ort-Besprechung) anlegen, optional gekoppelt an Fachobjekte (`refType`, `refId`). |
| `appointment_edit` | GET\|POST `/vorgaenge/termine/{id}/bearbeiten` | `AppointmentController::edit()` | Termindaten (Zeitraum, Titel, Teilnehmer, Notizen) aktualisieren. |

---

### 2.4 Abrechnung

| Route | Methode, URL | Controller | Was passiert fachlich? |
|---|---|---|---|
| `charge_index` | ANY `/abrechnung/sollstellungen` | `AccountingController::charges()` | Monatliche Sollstellungen (Kaltmiete und Nebenkostenvorauszahlung) für einen gewählten Monat auflisten. |
| `charge_generate` | POST `/abrechnung/sollstellungen/erzeugen` | `AccountingController::generate() → TenancyService::generateMonthlyCharges()` | Monatliche Sollstellungen für alle aktiven Mietverhältnisse im Zielmonat stapelweise erzeugen (verhindert Dubletten). |
| `account_index` | ANY `/abrechnung/mieterkonten` | `AccountingController::accounts() → AccountingService::getTenantAccount()` | Übersicht aller Mieterkonten mit aktuellem Saldo (Forderungen abzüglich Zahlungen) für laufende Verträge anzeigen. |
| `account_show` | GET\|POST `/abrechnung/mieterkonten/{id}` | `AccountingController::account() → AccountingService::recordPayment()` | Mieterkonto eines Mietverhältnisses mit Historie aller Sollstellungen und Zahlungen anzeigen sowie manuelle Mietzahlung erfassen. |
| `booking_index` | ANY `/abrechnung/buchungen` | `AccountingController::bookings()` | Journal der letzten 200 Einnahmen- und Ausgabenbuchungen (Instandhaltung, Rechnungen etc.) anzeigen. |
| `booking_new` | GET\|POST `/abrechnung/buchungen/neu` | `AccountingController::newBooking()` | Neue Liegenschaftsbuchung erfassen; ordnet die Buchung automatisch dem Eigentümer der Liegenschaft zu. |
| `owner_statement` | ANY `/abrechnung/eigentuemerabrechnung` | `AccountingController::statement() → AccountingService::ownerStatement()` | Monatliche Eigentümerabrechnung erstellen: saldiert Mieteinnahmen, Ausgaben und Verwaltungshonorar zu einem Auszahlungsbetrag. |

---

### 2.5 Übergreifende Funktionen (Dashboard, Berichte, Verwaltung, Sicherheit)

| Route | Methode, URL | Controller | Was passiert fachlich? |
|---|---|---|---|
| `dashboard` | ANY `/` | `DashboardController::index()` | Management-Dashboard aufrufen: Kennzahlen zu Einheiten (Gesamt, frei, gekündigt), Verträgen, offenen Meldungen, Aufgaben und anstehenden Terminen. |
| `report_vacancy` | ANY `/berichte/leerstand` | `ReportController::vacancy() → PropertyService::getStatistics()` | Leerstandsbericht je Liegenschaft anzeigen (Einheitenanzahl, vermietet, leerstehend, Leerstandsquote und Mietausfall). |
| `admin_users` | ANY `/verwaltung/benutzer` | `ReportController::users()` | Benutzerverwaltung aufrufen: Übersicht aller Benutzerkonten und deren Berechtigungsrollen. |
| `app_login` | ANY `/login` | `SecurityController::login()` | Anmeldeseite für Mitarbeiter bereitstellen und Authentifizierung durchführen. |
| `app_logout` | ANY `/logout` | `SecurityController::logout()` | Benutzer aus dem System abmelden und Benutzersitzung invalidieren. |

---

## 3. Gesamt-Routenverzeichnis (Referenztabelle)

Vollständige Tabelle aller **64 Routen** in der Reihenfolge von `bin/console debug:router`:

| Route | Methode, URL | Controller | Was passiert fachlich? |
|---|---|---|---|
| `charge_index` | ANY `/abrechnung/sollstellungen` | `AccountingController::charges()` | Monatliche Sollstellungen für gewählten Monat auflisten. |
| `charge_generate` | POST `/abrechnung/sollstellungen/erzeugen` | `AccountingController::generate() → TenancyService::generateMonthlyCharges()` | Monatliche Sollstellungen für alle aktiven Mietverhältnisse im Zielmonat generieren. |
| `account_index` | ANY `/abrechnung/mieterkonten` | `AccountingController::accounts() → AccountingService::getTenantAccount()` | Übersicht der Mieterkonten mit aktuellem Saldo anzeigen. |
| `account_show` | GET\|POST `/abrechnung/mieterkonten/{id}` | `AccountingController::account() → AccountingService::recordPayment()` | Mieterkonto mit Buchungsverlauf anzeigen und Mietzahlung erfassen. |
| `booking_index` | ANY `/abrechnung/buchungen` | `AccountingController::bookings()` | Die letzten 200 Liegenschaftsbuchungen (Einnahmen/Ausgaben) auflisten. |
| `booking_new` | GET\|POST `/abrechnung/buchungen/neu` | `AccountingController::newBooking()` | Neue Liegenschaftsbuchung erfassen und Eigentümer zuordnen. |
| `owner_statement` | ANY `/abrechnung/eigentuemerabrechnung` | `AccountingController::statement() → AccountingService::ownerStatement()` | Eigentümerabrechnung (Mieteinnahmen minus Ausgaben und Verwaltergebühr) berechnen. |
| `appointment_index` | ANY `/vorgaenge/termine/` | `AppointmentController::index()` | Kalendertermine anzeigen (anstehend oder alle). |
| `appointment_new` | GET\|POST `/vorgaenge/termine/neu` | `AppointmentController::new()` | Neuen Termin erfassen (optional mit Objekt-/Meldungsbezug). |
| `appointment_edit` | GET\|POST `/vorgaenge/termine/{id}/bearbeiten` | `AppointmentController::edit()` | Bestehenden Termin bearbeiten. |
| `dashboard` | ANY `/` | `DashboardController::index()` | Startseite mit Kennzahlen, offenen Meldungen, Aufgaben und Terminen anzeigen. |
| `issue_index` | ANY `/vorgaenge/meldungen/` | `IssueController::index()` | Schadensmeldungen anzeigen (Filter nach Status). |
| `issue_new` | GET\|POST `/vorgaenge/meldungen/neu` | `IssueController::new() → IssueService::createIssue()` | Schadensmeldung erfassen, Mieter/Objekt zuordnen, Statushistorie anlegen. |
| `issue_show` | ANY `/vorgaenge/meldungen/{id}` | `IssueController::show() → IssueService::getCurrentTenancy(), IssueService::getRelatedAppointments()` | Meldungsdetails mit Mietverhältnis, Aufgaben, Terminen und Statushistorie anzeigen. |
| `issue_edit` | GET\|POST `/vorgaenge/meldungen/{id}/bearbeiten` | `IssueController::edit()` | Stammdaten einer Meldung aktualisieren. |
| `issue_status` | POST `/vorgaenge/meldungen/{id}/status` | `IssueController::status()` | Status einer Meldung manuell ändern und mit Bearbeiter/Kommentar historisieren. |
| `issue_close` | POST `/vorgaenge/meldungen/{id}/schliessen` | `IssueController::close() → IssueService::closeIssue()` | Meldung schließen (nur zulässig, wenn keine offenen Aufgaben mehr existieren). |
| `issue_bulk` | POST `/vorgaenge/meldungen/sammelaenderung` | `IssueController::bulk()` | Status mehrerer ausgewählter Meldungen gleichzeitig ändern. |
| `issue_add_task` | POST `/vorgaenge/meldungen/{id}/aufgabe` | `IssueController::addTask() → IssueService::createTaskFromIssue()` | Aufgabe zu Meldung anlegen, Dienstleister vorschlagen und Meldung in Bearbeitung setzen. |
| `issue_print` | ANY `/vorgaenge/meldungen/{id}/druck` | `IssueController::print()` | Mieterbestätigung/Mitteilung der Meldung druckfertig anzeigen. |
| `prospect_index` | ANY `/vermietung/interessenten` | `LettingController::prospects()` | Chronologische Liste aller Mietinteressenten anzeigen. |
| `prospect_new` | GET\|POST `/vermietung/interessenten/neu` | `LettingController::newProspect()` | Neuen Mietinteressenten erfassen. |
| `prospect_edit` | GET\|POST `/vermietung/interessenten/{id}/bearbeiten` | `LettingController::editProspect()` | Interessentendaten bearbeiten. |
| `prospect_accept` | POST `/vermietung/interessenten/{id}/zusage` | `LettingController::accept() → RentalService::acceptProspect()` | Zusage erteilen: Mieter anlegen, Mieteinheit reservieren, zur Vertragserstellung leiten. |
| `viewing_index` | ANY `/vermietung/besichtigungen` | `LettingController::viewings()` | Besichtigungstermine auflisten. |
| `viewing_new` | GET\|POST `/vermietung/besichtigungen/neu` | `LettingController::newViewing() → RentalService::planViewing()` | Besichtigung planen, Interessentenstatus anpassen und Kalendertermin anlegen. |
| `owner_index` | ANY `/stammdaten/eigentuemer/` | `OwnerController::index()` | Liste der Immobilieneigentümer alphabetisch anzeigen. |
| `owner_new` | GET\|POST `/stammdaten/eigentuemer/neu` | `OwnerController::new()` | Neuen Eigentümer erfassen und Eigentümernummer (`EI-xxxx`) vergeben. |
| `owner_show` | ANY `/stammdaten/eigentuemer/{id}` | `OwnerController::show()` | Eigentümerdaten und zugeordnete Liegenschaften anzeigen. |
| `owner_edit` | GET\|POST `/stammdaten/eigentuemer/{id}/bearbeiten` | `OwnerController::edit()` | Eigentümerdaten bearbeiten. |
| `property_index` | ANY `/stammdaten/objekte/` | `PropertyController::index()` | Übersicht aller Liegenschaften anzeigen. |
| `property_new` | GET\|POST `/stammdaten/objekte/neu` | `PropertyController::new() → PropertyService::nextPropertyNumber()` | Neue Liegenschaft erfassen und Objektnummer (`OBJ-xxxx`) vergeben. |
| `property_show` | ANY `/stammdaten/objekte/{id}` | `PropertyController::show() → PropertyService::getStatistics(), AppointmentService::findFor()` | Liegenschaftsdetails, Kennzahlen, Gebäude und Liegenschaftstermine anzeigen. |
| `property_edit` | GET\|POST `/stammdaten/objekte/{id}/bearbeiten` | `PropertyController::edit()` | Stammdaten einer Liegenschaft bearbeiten. |
| `building_new` | GET\|POST `/stammdaten/objekte/{id}/gebaeude/neu` | `PropertyController::newBuilding()` | Neues Gebäude in einer Liegenschaft anlegen (`OBJ-xxxx-Gy`). |
| `building_edit` | GET\|POST `/stammdaten/objekte/gebaeude/{id}/bearbeiten` | `PropertyController::editBuilding()` | Gebäudedaten bearbeiten. |
| `unit_index` | ANY `/stammdaten/einheiten/` | `RentalUnitController::index()` | Übersicht aller Mieteinheiten anzeigen (Filter nach Status). |
| `unit_new` | GET\|POST `/stammdaten/einheiten/neu/{building}` | `RentalUnitController::new()` | Neue Mieteinheit zu einem Gebäude erfassen. |
| `unit_show` | ANY `/stammdaten/einheiten/{id}` | `RentalUnitController::show()` | Mieteinheitsdetails mit Vertrag, Meldungen, Interessenten und Terminen anzeigen. |
| `unit_edit` | GET\|POST `/stammdaten/einheiten/{id}/bearbeiten` | `RentalUnitController::edit()` | Mieteinheit bearbeiten und ändernden Benutzer festhalten. |
| `report_vacancy` | ANY `/berichte/leerstand` | `ReportController::vacancy() → PropertyService::getStatistics()` | Leerstandsbericht mit Leerstandsquoten und Mietausfall je Objekt anzeigen. |
| `admin_users` | ANY `/verwaltung/benutzer` | `ReportController::users()` | Benutzerverwaltung und Rollenübersicht anzeigen. |
| `app_login` | ANY `/login` | `SecurityController::login()` | Mitarbeiter-Login bereitstellen und Authentifizierung ausführen. |
| `app_logout` | ANY `/logout` | `SecurityController::logout()` | Mitarbeiter aus der aktuellen Sitzung abmelden. |
| `provider_index` | ANY `/stammdaten/dienstleister/` | `ServiceProviderController::index()` | Liste aller externen Handwerker und Dienstleister anzeigen. |
| `provider_new` | GET\|POST `/stammdaten/dienstleister/neu` | `ServiceProviderController::new()` | Neuen Dienstleister mit Gewerk und Ansprechpartner anlegen. |
| `provider_edit` | GET\|POST `/stammdaten/dienstleister/{id}/bearbeiten` | `ServiceProviderController::edit()` | Dienstleisterdaten aktualisieren. |
| `task_index` | ANY `/vorgaenge/aufgaben/` | `TaskController::index()` | Aufgabenübersicht anzeigen (offene oder alle Aufgaben). |
| `task_new` | GET\|POST `/vorgaenge/aufgaben/neu` | `TaskController::new() → TaskService::save()` | Neue Aufgabe erfassen, Nummer (`AUF-xxxx`) vergeben und Dienstleister prüfen. |
| `task_edit` | GET\|POST `/vorgaenge/aufgaben/{id}/bearbeiten` | `TaskController::edit() → TaskService::save()` | Aufgabe (Status, Fälligkeit, Kosten, Dienstleister) bearbeiten. |
| `task_done` | POST `/vorgaenge/aufgaben/{id}/erledigt` | `TaskController::done()` | Aufgabe als erledigt markieren (`DONE`). |
| `task_reopen` | POST `/vorgaenge/aufgaben/{id}/wiedereroeffnen` | `TaskController::reopen()` | Erledigte Aufgabe mit Begründung wieder öffnen (`OPEN`). |
| `tenancy_index` | ANY `/vermietung/mietverhaeltnisse` | `TenancyController::index()` | Mietverhältnisse auflisten (Filter: aktuell, alle, Status). |
| `tenancy_new` | GET\|POST `/vermietung/mietverhaeltnisse/neu/{unit}` | `TenancyController::new() → TenancyService::createTenancy()` | Mietvertragsentwurf (`DRAFT`) anlegen und Einheit reservieren. |
| `tenancy_show` | ANY `/vermietung/mietverhaeltnisse/{id}` | `TenancyController::show()` | Vertragsdetails, Mieter, Saldo, Übergaben und Termine anzeigen. |
| `tenancy_activate` | POST `/vermietung/mietverhaeltnisse/{id}/aktivieren` | `TenancyController::activate() → TenancyService::activate()` | Entwurf eines Mietvertrags wird wirksam: Vertrag wird `ACTIVE`, Einheit `RENTED`, Einzugstermin und erste Sollstellung werden erzeugt. |
| `tenancy_tenant` | POST `/vermietung/mietverhaeltnisse/{id}/mieter` | `TenancyController::tenants() → TenancyService::addTenant() / removeTenant()` | Mieter zu Vertrag hinzufügen oder entfernen (letzter Mieter kann nicht entfernt werden). |
| `tenancy_terminate` | POST `/tenancies/{id}/terminate` | `TenancyController::terminate()` | Kündigung eines aktiven Mietverhältnisses wird erfasst: setzt Kündigungsdatum, berechnet Ende (+3 Monate), setzt Status `NOTICE_GIVEN` und plant Auszugstermin. |
| `tenancy_terminate_immediately` | POST `/vermietung/mietverhaeltnisse/{id}/aufheben` | `TenancyController::terminateImmediately() → TenancyService::terminateImmediately()` | Mietverhältnis außerordentlich/fristlos beenden (`TERMINATED`) und Übergabetermin planen. |
| `handover_new` | GET\|POST `/vermietung/mietverhaeltnisse/{id}/uebergabe` | `TenancyController::handover() → TenancyService::recordHandover()` | Übergabeprotokoll (Einzug/Auszug) erfassen; bei Auszug Vertrag auf `ENDED` setzen und Einheit auf `AVAILABLE` oder `RENOVATION` setzen. |
| `tenant_index` | ANY `/stammdaten/mieter/` | `TenantController::index()` | Liste aller Mieter anzeigen (Suche nach Name). |
| `tenant_new` | GET\|POST `/stammdaten/mieter/neu` | `TenantController::new()` | Neuen Mieter manuell erfassen. |
| `tenant_show` | ANY `/stammdaten/mieter/{id}` | `TenantController::show()` | Mieterdetails und verknüpfte Verträge einsehen. |
| `tenant_edit` | GET\|POST `/stammdaten/mieter/{id}/bearbeiten` | `TenantController::edit()` | Mieterdaten und Notizen bearbeiten. |
