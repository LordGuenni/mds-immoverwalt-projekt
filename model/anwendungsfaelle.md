# Anwendungsfälle Altsystem (ImmoVerwalt)

Dieses Dokument enthält die fachlichen Anwendungsfälle (Use Cases) des Altsystems ImmoVerwalt, abgeleitet aus:
1. **Bestandsaufnahme:** [`model/bestand.md`](file:///home/florian/Developement/mds-immoverwalt-projekt/model/bestand.md) (Routen, Controller und Doctrine-Entitäten)
2. **Benutzeroberfläche:** Hauptmenü, Untermenüs, Aktions-Buttons und Formulare (`templates/`)
3. **Rollenmodell:**
   - `verwalter` (`ROLE_PROPERTY_MANAGER`): Operative Liegenschafts-, Bestands- und Vorgangsverwaltung, Schadensmeldungen, Aufgaben, Abrechnung.
   - `vermieter` (`ROLE_LETTING_MANAGER`): Interessentenbetreuung, Besichtigungen, Mietvertragsentwürfe und Aktivierung.
   - `admin` (`ROLE_ADMIN`): Übergeordnete Rechte (inkl. Benutzerverwaltung, fristlose Aufhebung von Verträgen).

Reine technische Funktionen (Login/Logout, interne Firewall-Weiterleitungen, reine Lese-Listen ohne Fachlogik) wurden ausgeklammert oder mit den fachlichen Aktionen zusammengefasst.

---

## 1. Tabelle der fachlichen Anwendungsfälle

| Nr. | Anwendungsfall | Akteur | Ziel | Routen | Bereich |
|---|---|---|---|---|---|
| **AF-01** | Mietinteressent erfassen | Vermieter | Kontaktdaten und Wohnwünsche eines Interessenten aufnehmen | `prospect_new`, `prospect_edit` | Vermietung |
| **AF-02** | Besichtigung terminieren | Vermieter | Besichtigungstermin vereinbaren und im Kalender vormerken | `viewing_new` | Vermietung |
| **AF-03** | Zusage erteilen und reservieren | Vermieter | Interessenten akzeptieren, Mieteinheit reservieren und als Mieter anlegen | `prospect_accept` | Vermietung |
| **AF-04** | Mietvertragsentwurf anlegen | Vermieter | Mietvertragskonditionen festlegen und Einheit reservieren | `tenancy_new`, `tenancy_show` | Vermietung |
| **AF-05** | Vertragsparteien verwalten | Vermieter / Verwalter | Mieter zu einem Mietvertrag hinzufügen oder entfernen | `tenancy_tenant` | Vermietung |
| **AF-06** | Mietverhältnis aktivieren | Vermieter | Unterzeichneten Vertrag wirksam setzen, Einheit vermieten, Einzug terminieren | `tenancy_activate` | Vermietung |
| **AF-07** | Mietverhältnis kündigen | Verwaltung | Kündigung mit Frist erfassen, Übergabe planen | `tenancy_terminate` | Vermietung |
| **AF-08** | Mietverhältnis fristlos aufheben | Admin | Vertrag außerordentlich beenden und sofortige Rückgabe veranlassen | `tenancy_terminate_immediately` | Vermietung |
| **AF-09** | Wohnungsübergabe protokollieren | Verwalter | Einzug oder Auszug mit Zählerständen dokumentieren und Einheit freigeben/sperren | `handover_new` | Vermietung |
| **AF-10** | Liegenschaft anlegen und pflegen | Verwalter | Liegenschaft mit Adresse, Eigentümer und Honorarsatz verwalten | `property_new`, `property_edit` | Bestand |
| **AF-11** | Gebäude strukturieren | Verwalter | Gebäude innerhalb einer Liegenschaft erfassen oder bearbeiten | `building_new`, `building_edit` | Bestand |
| **AF-12** | Mieteinheit anlegen und pflegen | Verwalter | Wohnung/Gewerbe mit Fläche, Zimmern und Mietansätzen einrichten | `unit_new`, `unit_edit` | Bestand |
| **AF-13** | Eigentümer verwalten | Verwalter | Immobilieneigentümer mit Bankverbindung und Kontaktdaten hinterlegen | `owner_new`, `owner_edit` | Bestand |
| **AF-14** | Mieterstammdaten pflegen | Verwalter | Mieterdaten und Notizen zentral erfassen und aktualisieren | `tenant_new`, `tenant_edit` | Bestand |
| **AF-15** | Dienstleister pflegen | Verwalter | Handwerker mit Gewerk und Ansprechpartner für Aufträge registrieren | `provider_new`, `provider_edit` | Bestand |
| **AF-16** | Schadensmeldung erfassen | Verwalter | Mangel aufnehmen, Objekt/Mieter zuordnen, Dringlichkeit prüfen | `issue_new`, `issue_edit` | Meldungen/Aufgaben |
| **AF-17** | Meldungsstatus fortschreiben | Verwalter | Bearbeitungsstand einer Meldung mit Begründung dokumentieren | `issue_status`, `issue_bulk` | Meldungen/Aufgaben |
| **AF-18** | Handwerkeraufgabe beauftragen | Verwalter | Reparaturaufgabe ableiten, Dienstleister zuordnen und Meldung in Arbeit setzen | `issue_add_task` | Meldungen/Aufgaben |
| **AF-19** | Meldung schließen | Verwalter | Meldung nach vollständiger Erledigung aller Aufgaben formal beenden | `issue_close` | Meldungen/Aufgaben |
| **AF-20** | Mietermitteilung drucken | Verwalter | Schriftliche Vorgangsbestätigung für den Mieter erzeugen | `issue_print` | Meldungen/Aufgaben |
| **AF-21** | Aufgabe planen und steuern | Verwalter | Einzelaufgabe mit Fälligkeit, Priorität und Kosten pflegen | `task_new`, `task_edit` | Meldungen/Aufgaben |
| **AF-22** | Aufgabe abschließen | Verwalter | Durchgeführte Instandhaltungsaufgabe als erledigt kennzeichnen | `task_done` | Meldungen/Aufgaben |
| **AF-23** | Aufgabe wiedereröffnen | Verwalter | Erledigte Aufgabe bei Nacharbeit mit Begründung reaktivieren | `task_reopen` | Meldungen/Aufgaben |
| **AF-24** | Vor-Ort-Termin vereinbaren | Verwalter | Kalendertermin für Besprechung, Besichtigung oder Handwerker planen | `appointment_new`, `appointment_edit` | Termine |
| **AF-25** | Monatliche Sollstellungen erzeugen | Verwalter | Miet- und Nebenkostenforderungen aller aktiven Verträge monatlich buchen | `charge_generate` | Abrechnung |
| **AF-26** | Mietzahlung verbuchen | Verwalter | Zahlungseingang auf Mieterkonto erfassen und Saldo ausgleichen | `account_show` | Abrechnung |
| **AF-27** | Liegenschaftsbuchung erfassen | Verwalter | Einnahme oder Ausgabe buchen und Eigentümer zuordnen | `booking_new` | Abrechnung |
| **AF-28** | Eigentümerabrechnung erstellen | Verwalter | Einnahmen, Ausgaben und Verwaltungshonorar für Eigentümer saldieren | `owner_statement` | Abrechnung |
| **AF-29** | Leerstand überwachen | Verwalter | Leerstandsquote und entgangene Mieteinnahmen je Objekt analysieren | `report_vacancy` | Berichte |

---

## 2. Zuordnung zur Benutzeroberfläche (Menüs, Buttons, Formulare)

| Nr. | Menüpfad | Einstiegsbutton / Oberflächenaktion | Zugehörige Rolle |
|---|---|---|---|
| **AF-01** | Vermietung > Interessenten | Button `+ Neuer Interessent` | `vermieter` |
| **AF-02** | Vermietung > Interessenten / Besichtigungen | Button `Besichtigung planen` | `vermieter` |
| **AF-03** | Vermietung > Interessenten | Button `Zusage` | `vermieter` |
| **AF-04** | Stammdaten > Mieteinheiten > Detail | Button `Mietvertrag anlegen` (bei freien/reservierten Einheiten) | `vermieter` |
| **AF-05** | Vermietung > Mietverhältnisse > Detail | Formular `Mieter hinzufügen` bzw. Button `Entfernen` | `vermieter`, `verwalter` |
| **AF-06** | Vermietung > Mietverhältnisse > Detail | Button `Vertrag unterschrieben - aktivieren` | `vermieter` |
| **AF-07** | Vermietung > Mietverhältnisse > Detail | Formular + Button `Kündigung erfassen` | `verwalter` (Verwaltung) |
| **AF-08** | Vermietung > Mietverhältnisse > Detail | Button `Aufhebung / fristlos` (Sicherheitsabfrage, nur Admin) | `admin` |
| **AF-09** | Vermietung > Mietverhältnisse > Detail | Button `Übergabe / Rückgabe` | `verwalter` |
| **AF-10** | Stammdaten > Objekte | Button `+ Neues Objekt` bzw. `Objekt bearbeiten` | `verwalter` |
| **AF-11** | Stammdaten > Objekte > Detail | Button `+ Neues Gebäude` bzw. `Gebäude bearbeiten` | `verwalter` |
| **AF-12** | Stammdaten > Mieteinheiten | Button `+ Neue Einheit` bzw. `Mieteinheit bearbeiten` | `verwalter` |
| **AF-13** | Stammdaten > Eigentümer | Button `+ Neuer Eigentümer` bzw. `Eigentümer bearbeiten` | `verwalter` |
| **AF-14** | Stammdaten > Mieter | Button `+ Neuer Mieter` bzw. `Mieter bearbeiten` | `verwalter` |
| **AF-15** | Stammdaten > Dienstleister | Button `+ Neuer Dienstleister` bzw. `Dienstleister bearbeiten` | `verwalter` |
| **AF-16** | Vorgänge > Meldungen | Button `+ Neue Meldung erfassen` | `verwalter` |
| **AF-17** | Vorgänge > Meldungen > Detail / Liste | Auswahlliste + Button `Status ändern` bzw. Sammelaktion | `verwalter` |
| **AF-18** | Vorgänge > Meldungen > Detail | Formular + Button `Aufgabe anlegen` | `verwalter` |
| **AF-19** | Vorgänge > Meldungen > Detail | Button `Meldung schließen` (prüft offene Aufgaben) | `verwalter` |
| **AF-20** | Vorgänge > Meldungen > Detail | Button `Drucken` (öffnet Druckansicht) | `verwalter` |
| **AF-21** | Vorgänge > Aufgaben | Button `+ Neue Aufgabe` bzw. `Aufgabe bearbeiten` | `verwalter` |
| **AF-22** | Vorgänge > Aufgaben > Bearbeiten | Button `Erledigt` | `verwalter` |
| **AF-23** | Vorgänge > Aufgaben > Bearbeiten | Formular + Button `Wiedereröffnen` (mit Begründung) | `verwalter` |
| **AF-24** | Vorgänge > Termine | Button `+ Neuer Termin` bzw. `Termin bearbeiten` | `verwalter` |
| **AF-25** | Abrechnung > Sollstellungen | Monatsauswahl + Button `Sollstellungen erzeugen` | `verwalter` |
| **AF-26** | Abrechnung > Mieterkonten > Detail | Formular + Button `Zahlung erfassen` | `verwalter` |
| **AF-27** | Abrechnung > Buchungen | Button `+ Neue Buchung` | `verwalter` |
| **AF-28** | Abrechnung > Eigentümerabrechnung | Objektauswahl + Button `Abrechnung berechnen` | `verwalter` |
| **AF-29** | Berichte > Leerstand | Menüpunkt `Leerstand` | `verwalter` |

---

## 3. Prüffrage: Lässt sich jeder Anwendungsfall in einem Satz aus Sicht des Akteurs formulieren?

**Ja.** Jeder Anwendungsfall beschreibt ein abgeschlossenes fachliches Ziel eines handelnden Akteurs:

- **AF-01:** *Als Vermieter möchte ich Mietinteressenten mit Kontaktdaten und Wunschkriterien erfassen, um passende Mietangebote zu unterbreiten.*
- **AF-02:** *Als Vermieter möchte ich Besichtigungstermine mit Interessenten vereinbaren, um Wohnungsbegehungen verbindlich zu koordinieren.*
- **AF-03:** *Als Vermieter möchte ich einem Interessenten verbindlich zusagen, um die Mieteinheit zu reservieren und die Person als Mieter in den Vertragsprozess zu übernehmen.*
- **AF-04:** *Als Vermieter möchte ich einen Mietvertragsentwurf mit verbindlichen Mietkonditionen erstellen, um die Verhandlungsgrundlage für den Abschluss festzuhalten.*
- **AF-05:** *Als Vermieter oder Verwalter möchte ich Vertragsparteien bearbeiten, um Mieterwechsel im Vertrag abzubilden, ohne den letzten Mieter eines aktiven Vertrages zu löschen.*
- **AF-06:** *Als Vermieter möchte ich den unterzeichneten Vertrag aktivieren, um die Einheit als vermietet zu buchen, die Einzugsübergabe zu planen und monatliche Sollstellungen auszulösen.*
- **AF-07:** *Als Verwalter möchte ich die Kündigung eines Mieters mit Frist erfassen, um das Vertragsende festzulegen, die Einheit als gekündigt zu markieren und die Wohnungsrückgabe zu planen.*
- **AF-08:** *Als Administrator möchte ich ein Mietverhältnis fristlos aufheben, um ein gestörtes Vertragsverhältnis sofort zu beenden und die Rückgabe einzuleiten.*
- **AF-09:** *Als Verwalter möchte ich Ein- und Auszüge per Übergabeprotokoll dokumentieren, um Zählerstände und Mängel festzuhalten und die Einheit freizugeben oder in Renovierung zu setzen.*
- **AF-10:** *Als Verwalter möchte ich Liegenschaften mit Stammdaten und Eigentümerzuordnung anlegen, um die Verwaltungsgrundlage für Gebäude und Verträge bereitzustellen.*
- **AF-11:** *Als Verwalter möchte ich Gebäude innerhalb einer Liegenschaft strukturieren, um Mieteinheiten baulichen Objekten zuzuordnen.*
- **AF-12:** *Als Verwalter möchte ich Mieteinheiten mit Flächen und Mietansätzen pflegen, um Vermietungs- und Abrechnungsprozesse auf korrekten Stammdaten auszuführen.*
- **AF-13:** *Als Verwalter möchte ich Eigentümer mit Anschrift und Bankverbindung erfassen, um Abrechnungen und Honorare korrekt abrechnen zu können.*
- **AF-14:** *Als Verwalter möchte ich Mieterstammdaten zentral pflegen, um Kontaktdaten für Vorgänge und Verträge aktuell zu halten.*
- **AF-15:** *Als Verwalter möchte ich Handwerksbetriebe nach Gewerken verwalten, um Instandhaltungsaufträge gezielt an qualifizierte Partner zu vergeben.*
- **AF-16:** *Als Verwalter möchte ich eingehende Schadensmeldungen erfassen, um Mängel zu dokumentieren, Einheit und Mieter zuzuordnen und eilige Fälle sofort in Prüfung zu nehmen.*
- **AF-17:** *Als Verwalter möchte ich den Bearbeitungsstatus von Meldungen mit Kommentar fortschreiben, um den Bearbeitungsfortschritt transparent zu historisieren.*
- **AF-18:** *Als Verwalter möchte ich Reparaturaufgaben direkt aus einer Meldung ableiten, um Handwerker anhand des Gewerks zu beauftragen und die Meldung in Bearbeitung zu setzen.*
- **AF-19:** *Als Verwalter möchte ich eine Meldung schließen, sobald alle zugehörigen Aufgaben erledigt sind, um den Vorgang ordnungsgemäß abzuschließen.*
- **AF-20:** *Als Verwalter möchte ich dem Mieter eine schriftliche Bestätigung der Mängelmeldung ausdrucken, um den Mieter über den Vorgangsstand zu informieren.*
- **AF-21:** *Als Verwalter möchte ich Arbeitsaufgaben mit Frist und Kostenverfolgung steuern, um Mängel zeitnah und wirtschaftlich zu beheben.*
- **AF-22:** *Als Verwalter möchte ich erledigte Aufgaben abschließen, um den Arbeitsfortschritt zu dokumentieren und den Weg zur Meldungsschließung freizugeben.*
- **AF-23:** *Als Verwalter möchte ich eine erledigte Aufgabe bei Mängeln unter Angabe einer Begründung wiedereröffnen, um Nachbesserungen verbindlich einzufordern.*
- **AF-24:** *Als Verwalter möchte ich Termine mit Objektbezug vereinbaren, um Vor-Ort-Aktivitäten termingerecht im Team zu koordinieren.*
- **AF-25:** *Als Verwalter möchte ich monatliche Sollstellungen erzeugen, um Miet- und Nebenkostenforderungen für alle laufenden Verträge gebündelt zu buchen.*
- **AF-26:** *Als Verwalter möchte ich eingehende Mietzahlungen verbuchen, um Mieterkonten auszugleichen und Zahlungsrückstände unmittelbar zu erkennen.*
- **AF-27:** *Als Verwalter möchte ich Einnahmen und Ausgaben einer Liegenschaft erfassen, um Instandhaltungskosten dem Eigentümer zuzuordnen.*
- **AF-28:** *Als Verwalter möchte ich monatliche Eigentümerabrechnungen erstellen, um Mieteinnahmen gegen Kosten und Honorar aufzurechnen und den Auszahlungsbetrag zu ermitteln.*
- **AF-29:** *Als Verwalter möchte ich Leerstandsberichte abrufen, um Ertragsausfälle zu analysieren und Nachvermietungen anzustoßen.*

---

## 4. Ausgeklammerte Technik und reine Navigation

Die folgenden Routen wurden nicht als eigene fachliche Anwendungsfälle modelliert:
- **`app_login`, `app_logout`**: Reine Authentifizierungsinfrastruktur der Symfony-Firewall; kein fachlicher Geschäftsvorfall der Hausverwaltung.
- **`admin_users`**: Reine Systembenutzer-Liste für Systemadministratoren.
- **`dashboard`**: Reines Informationsportal zur aggregierten Übersicht über Bestände und Termine.
- **Listenansichten (`*_index`) und Detailseiten (`*_show`)**: Dienen als Navigations- und Einstiegspunkte für die Aktionen, stellen jedoch für sich allein keinen zustandsändernden Geschäftsvorfall dar.
