# Anwendungsfälle Altsystem (ImmoVerwalt)

Dieses Dokument enthält die fachlichen Anwendungsfälle des Altsystems ImmoVerwalt, abgeleitet aus:
1. **Bestandsaufnahme:** [`bestand.md`](./bestand.md) (Routen, Controller und Entitäten)
2. **Benutzeroberfläche:** Hauptmenü, Untermenüs, Aktions-Buttons und Formulare (`templates/`)
3. **Rollenmodell** (`config/packages/security.yaml`, Benutzer aus `src/DataFixtures/AppFixtures.php`):
   - `verwalter` (`ROLE_PROPERTY_MANAGER`): Bestand, Meldungen, Aufgaben, Termine, Abrechnung.
   - `vermieter` (`ROLE_LETTING_MANAGER`): Mietinteressenten, Besichtigungen, Mietverhältnisse anlegen und aktivieren.
   - `admin` (`ROLE_ADMIN`): erbt beide Rollen; zusätzlich Benutzerübersicht und fristlose Aufhebung.

**Annahme zu den Akteuren:** Die Spalte *Akteur* ordnet jeden Anwendungsfall der Rolle zu, zu der er fachlich
gehört (Rollenname, Menü, Beschriftung). Das Altsystem **erzwingt diese Zuordnung nicht**: Laut
`security.yaml` darf jeder angemeldete Benutzer (`ROLE_USER`) alle Routen außer `/verwaltung` aufrufen.
Die einzigen rollenabhängigen Stellen sind der Menüpunkt *Verwaltung*, der Button *Aufhebung / fristlos*
(`templates/tenancy/show.html.twig`, nur Anzeige) und die internen Notizen einer Meldung, die
`vermieter` nicht sieht (`templates/issue/_details.html.twig`). Siehe REGEL-20 und Logbuch.

Reine Technik (Anmeldung, Abmeldung, Benutzerübersicht) und reine Lese-Ansichten ohne Fachlogik
wurden weggelassen bzw. in Abschnitt 4 den Anwendungsfällen als Einstieg zugeordnet.

---

## 1. Tabelle der fachlichen Anwendungsfälle

| Nr. | Anwendungsfall | Akteur | Ziel | Routen | Bereich |
|---|---|---|---|---|---|
| **AF-01** | Mietinteressent erfassen | Vermieter | Kontaktdaten und gewünschte Mieteinheit eines Interessenten aufnehmen und pflegen | `prospect_new`, `prospect_edit` | Vermietung |
| **AF-02** | Besichtigung planen | Vermieter | Besichtigungstermin mit einem Interessenten vereinbaren und im Kalender eintragen | `viewing_new` | Vermietung |
| **AF-03** | Zusage erteilen | Vermieter | Interessenten zusagen, als Mieter übernehmen und die gewünschte Mieteinheit reservieren | `prospect_accept` | Vermietung |
| **AF-04** | Mietverhältnis als Entwurf anlegen | Vermieter | Konditionen (Beginn, Miete, Kaution, Mieter) für eine freie oder reservierte Mieteinheit festlegen | `tenancy_new` | Vermietung |
| **AF-05** | Mieter eines Mietverhältnisses ändern | Vermieter | Mieter zu einem Mietverhältnis hinzufügen oder daraus entfernen | `tenancy_tenant` | Vermietung |
| **AF-06** | Mietverhältnis aktivieren | Vermieter | Unterschriebenes Mietverhältnis wirksam setzen, Mieteinheit vermieten, Einzug planen, erste Sollstellung erzeugen | `tenancy_activate` | Vermietung |
| **AF-07** | Mietverhältnis kündigen | Verwalter | Kündigung mit Frist erfassen und die Rückgabe planen | `tenancy_terminate` | Vermietung |
| **AF-08** | Mietverhältnis fristlos aufheben | Admin | Mietverhältnis zu einem Stichtag außerordentlich beenden und die Rückgabe planen | `tenancy_terminate_immediately` | Vermietung |
| **AF-09** | Übergabe protokollieren | Verwalter | Einzug oder Auszug mit Zählerständen, Schlüsseln und Mängeln dokumentieren; bei Auszug Mietverhältnis beenden und Mieteinheit freigeben oder in Renovierung setzen | `handover_new` | Vermietung |
| **AF-10** | Liegenschaft anlegen und pflegen | Verwalter | Liegenschaft mit Adresse, Eigentümer, Baujahr und Verwaltungsbeginn erfassen bzw. die Verwaltung beenden | `property_new`, `property_edit` | Bestand |
| **AF-11** | Gebäude anlegen und pflegen | Verwalter | Gebäude innerhalb einer Liegenschaft erfassen oder bearbeiten | `building_new`, `building_edit` | Bestand |
| **AF-12** | Mieteinheit anlegen und pflegen | Verwalter | Wohnung oder Gewerbeeinheit mit Etage, Fläche, Angebotsmiete und Status einrichten | `unit_new`, `unit_edit` | Bestand |
| **AF-13** | Eigentümer pflegen | Verwalter | Eigentümer mit Anschrift, Ansprechpartner und Kontaktdaten hinterlegen | `owner_new`, `owner_edit` | Bestand |
| **AF-14** | Mieter pflegen | Verwalter | Personen- und Kontaktdaten von Mietern erfassen und aktuell halten | `tenant_new`, `tenant_edit` | Bestand |
| **AF-15** | Dienstleister pflegen | Verwalter | Dienstleister mit Gewerk und Ansprechpartner erfassen oder deaktivieren | `provider_new`, `provider_edit` | Bestand |
| **AF-16** | Meldung erfassen | Verwalter | Mangel oder Anliegen aufnehmen, Liegenschaft/Mieteinheit/Mieter zuordnen, Dringlichkeit festlegen | `issue_new`, `issue_edit` | Meldungen/Aufgaben |
| **AF-17** | Meldungsstatus ändern | Verwalter | Bearbeitungsstand einer oder mehrerer Meldungen fortschreiben | `issue_status`, `issue_bulk` | Meldungen/Aufgaben |
| **AF-18** | Aufgabe zu einer Meldung anlegen | Verwalter | Aufgabe aus einer Meldung ableiten, Dienstleister nach Gewerk zuordnen, Meldung in Bearbeitung setzen | `issue_add_task` | Meldungen/Aufgaben |
| **AF-19** | Meldung schließen | Verwalter | Meldung nach Erledigung aller Aufgaben abschließen | `issue_close` | Meldungen/Aufgaben |
| **AF-20** | Mitteilung an Mieter drucken | Verwalter | Schriftliche Bestätigung einer Meldung für den Mieter erzeugen | `issue_print` | Meldungen/Aufgaben |
| **AF-21** | Aufgabe anlegen und pflegen | Verwalter | Aufgabe mit Fälligkeit, Priorität, Dienstleister und Notizen planen | `task_new`, `task_edit` | Meldungen/Aufgaben |
| **AF-22** | Aufgabe erledigen | Verwalter | Durchgeführte Aufgabe als erledigt kennzeichnen | `task_done` | Meldungen/Aufgaben |
| **AF-23** | Aufgabe wiedereröffnen | Verwalter | Erledigte Aufgabe bei Nacharbeit mit Begründung wieder öffnen | `task_reopen` | Meldungen/Aufgaben |
| **AF-24** | Termin planen | Verwalter | Termin (Wartung, Ablesung, Handwerker, Sonstiges) mit Bezug zu Bestand oder Vorgang planen | `appointment_new`, `appointment_edit` | Termine |
| **AF-25** | Monatliche Sollstellungen erzeugen | Verwalter | Kaltmiete und Nebenkostenvorauszahlung aller laufenden Mietverhältnisse für einen Monat fordern | `charge_generate` | Abrechnung |
| **AF-26** | Mietzahlung verbuchen | Verwalter | Zahlungseingang auf dem Mieterkonto erfassen und offenen Sollstellungen zuordnen | `account_show` | Abrechnung |
| **AF-27** | Liegenschaftsbuchung erfassen | Verwalter | Einnahme oder Ausgabe einer Liegenschaft buchen und dem Eigentümer zuordnen | `booking_new` | Abrechnung |
| **AF-28** | Eigentümerabrechnung erstellen | Verwalter | Mieteinnahmen, Ausgaben und Verwaltungshonorar einer Liegenschaft für einen Monat saldieren | `owner_statement` | Abrechnung |
| **AF-29** | Leerstand auswerten | Verwalter | Leerstandsquote und Mietausfall je Liegenschaft ermitteln | `report_vacancy` | Berichte |

Abrechnung (AF-25 bis AF-28), Termine (AF-24) und Berichte (AF-29) gehören nicht zu den Pflichtbereichen;
sie sind der Vollständigkeit halber erfasst, werden in den folgenden Schritten aber nur bei Bedarf vertieft.

---

## 2. Zuordnung zur Benutzeroberfläche

| Nr. | Menüpfad | Einstieg in der Oberfläche | Rolle (fachlich) |
|---|---|---|---|
| **AF-01** | Vermietung > Interessenten | Button `Neu` bzw. `Bearbeiten` | `vermieter` |
| **AF-02** | Vermietung > Interessenten / Besichtigungen | Button `Besichtigung` in der Interessentenliste bzw. `Neu` in der Besichtigungsliste | `vermieter` |
| **AF-03** | Vermietung > Interessenten | Button `Zusage` (nur bei Status neu, Besichtigung geplant, Bewerbung eingegangen) | `vermieter` |
| **AF-04** | Stammdaten > Mieteinheiten > Detail | Button `Mietvertrag anlegen` (nur bei freier Einheit); nach einer Zusage automatische Weiterleitung, dann auch bei reservierter Einheit | `vermieter` |
| **AF-05** | Vermietung > Mietverhältnisse > Detail | Auswahlliste + `Mieter hinzufügen` bzw. `entfernen` | `vermieter` |
| **AF-06** | Vermietung > Mietverhältnisse > Detail | Button `Vertrag unterschrieben - aktivieren` (nur im Entwurf) | `vermieter` |
| **AF-07** | Vermietung > Mietverhältnisse > Detail | Datumsfeld + `Kündigung erfassen` (nur bei aktivem Mietverhältnis) | `verwalter` |
| **AF-08** | Vermietung > Mietverhältnisse > Detail | Datumsfeld + `Aufhebung / fristlos` mit Sicherheitsabfrage (nur für Admin angezeigt) | `admin` |
| **AF-09** | Vermietung > Mietverhältnisse > Detail | Button `Übergabe / Rückgabe` | `verwalter` |
| **AF-10** | Stammdaten > Objekte | Button `Neu` bzw. `Ändern` | `verwalter` |
| **AF-11** | Stammdaten > Objekte > Detail | Button `Gebäude hinzufügen` bzw. `Ändern` | `verwalter` |
| **AF-12** | Stammdaten > Objekte > Detail (neu) bzw. Mieteinheiten > Detail (ändern) | Button `Einheit hinzufügen` bzw. `Ändern` | `verwalter` |
| **AF-13** | Stammdaten > Eigentümer | Button `Neu` bzw. `Ändern` | `verwalter` |
| **AF-14** | Stammdaten > Mieter | Button `Neu` bzw. `Ändern` | `verwalter` |
| **AF-15** | Stammdaten > Dienstleister | Button `Neu` bzw. `Bearbeiten` | `verwalter` |
| **AF-16** | Vorgänge > Meldungen | Button `Neue Meldung` (bzw. `Meldung erfassen` bei der Mieteinheit) bzw. `Ändern` | `verwalter` |
| **AF-17** | Vorgänge > Meldungen > Detail / Liste | Auswahlliste + `Status ändern` bzw. Sammeländerung in der Liste (`setzen`) | `verwalter` |
| **AF-18** | Vorgänge > Meldungen > Detail | Titelfeld + `anlegen` | `verwalter` |
| **AF-19** | Vorgänge > Meldungen > Detail | Button `Meldung schließen` | `verwalter` |
| **AF-20** | Vorgänge > Meldungen > Detail | Button `Mitteilung an Mieter drucken` | `verwalter` |
| **AF-21** | Vorgänge > Aufgaben | Button `Neue Aufgabe` bzw. Aufgabennummer anklicken | `verwalter` |
| **AF-22** | Vorgänge > Aufgaben > Aufgabe | Button `Als erledigt markieren` (nur bei offener Aufgabe) | `verwalter` |
| **AF-23** | Vorgänge > Aufgaben > Aufgabe | Begründung + `Wieder eröffnen` (nur bei erledigter Aufgabe) | `verwalter` |
| **AF-24** | Vorgänge > Termine | Button `Neuer Termin` bzw. `Bearbeiten` | `verwalter` |
| **AF-25** | Abrechnung > Sollstellungen | Monatsauswahl + `Sollstellungen für <Monat> erzeugen` | `verwalter` |
| **AF-26** | Abrechnung > Mieterkonten > Detail | Formular + `Zahlung buchen` | `verwalter` |
| **AF-27** | Abrechnung > Buchungen | Button `Neue Buchung` | `verwalter` |
| **AF-28** | Abrechnung > Eigentümerabrechnung | Objekt- und Monatsauswahl + `Abrechnung erstellen` | `verwalter` |
| **AF-29** | Berichte > Leerstand | Menüpunkt `Berichte` | `verwalter` |

---

## 3. Prüffrage: Lässt sich jeder Anwendungsfall in einem Satz aus Sicht des Akteurs formulieren?

**Ja.**

- **AF-01:** *Als Vermieter möchte ich Mietinteressenten mit Kontaktdaten und gewünschter Mieteinheit erfassen, um sie später kontaktieren und ihnen Mieteinheiten zeigen zu können.*
- **AF-02:** *Als Vermieter möchte ich eine Besichtigung mit einem Interessenten planen, damit der Termin verbindlich im Kalender steht.*
- **AF-03:** *Als Vermieter möchte ich einem Interessenten zusagen, damit er als Mieter übernommen und die Mieteinheit für ihn reserviert wird.*
- **AF-04:** *Als Vermieter möchte ich ein Mietverhältnis als Entwurf anlegen, um die Konditionen vor der Unterschrift festzuhalten.*
- **AF-05:** *Als Vermieter möchte ich die Mieter eines Mietverhältnisses ändern, um alle Vertragsparteien richtig abzubilden.*
- **AF-06:** *Als Vermieter möchte ich ein unterschriebenes Mietverhältnis aktivieren, damit die Mieteinheit als vermietet gilt und der Einzug geplant wird.*
- **AF-07:** *Als Verwalter möchte ich eine Kündigung erfassen, damit das Ende des Mietverhältnisses feststeht und die Rückgabe geplant wird.*
- **AF-08:** *Als Administrator möchte ich ein Mietverhältnis fristlos aufheben, um es zu einem Stichtag zu beenden.*
- **AF-09:** *Als Verwalter möchte ich Einzug und Auszug protokollieren, um Zählerstände, Schlüssel und Mängel festzuhalten.*
- **AF-10:** *Als Verwalter möchte ich Liegenschaften mit Eigentümer und Adresse pflegen, damit Gebäude, Mieteinheiten und Abrechnungen eine Grundlage haben.*
- **AF-11:** *Als Verwalter möchte ich Gebäude einer Liegenschaft anlegen, um Mieteinheiten zuordnen zu können.*
- **AF-12:** *Als Verwalter möchte ich Mieteinheiten mit Fläche und Angebotsmiete pflegen, damit sie vermietet werden können.*
- **AF-13:** *Als Verwalter möchte ich Eigentümer mit Kontaktdaten pflegen, um sie erreichen und ihnen Liegenschaften zuordnen zu können.*
- **AF-14:** *Als Verwalter möchte ich Mieterdaten pflegen, damit Kontaktdaten für Mietverhältnisse und Meldungen aktuell sind.*
- **AF-15:** *Als Verwalter möchte ich Dienstleister nach Gewerk pflegen, um Aufgaben an passende Betriebe vergeben zu können.*
- **AF-16:** *Als Verwalter möchte ich eine Meldung erfassen, um einen Mangel zu dokumentieren und dringende Fälle sofort prüfen zu lassen.*
- **AF-17:** *Als Verwalter möchte ich den Status von Meldungen ändern, damit der Bearbeitungsstand nachvollziehbar ist.*
- **AF-18:** *Als Verwalter möchte ich aus einer Meldung eine Aufgabe anlegen, damit ein passender Dienstleister den Mangel behebt.*
- **AF-19:** *Als Verwalter möchte ich eine Meldung schließen, sobald alle Aufgaben erledigt sind.*
- **AF-20:** *Als Verwalter möchte ich dem Mieter eine Bestätigung seiner Meldung drucken, um ihn zu informieren.*
- **AF-21:** *Als Verwalter möchte ich Aufgaben mit Fälligkeit und Dienstleister planen, damit sie rechtzeitig erledigt werden.*
- **AF-22:** *Als Verwalter möchte ich eine Aufgabe als erledigt kennzeichnen, damit die Meldung geschlossen werden kann.*
- **AF-23:** *Als Verwalter möchte ich eine erledigte Aufgabe mit Begründung wiedereröffnen, um Nacharbeit einzufordern.*
- **AF-24:** *Als Verwalter möchte ich Termine mit Bezug zu Bestand oder Vorgängen planen, um Arbeiten vor Ort zu koordinieren.*
- **AF-25:** *Als Verwalter möchte ich die Sollstellungen eines Monats erzeugen, damit für alle laufenden Mietverhältnisse die Miete gefordert wird.*
- **AF-26:** *Als Verwalter möchte ich Mietzahlungen verbuchen, damit der Saldo des Mieterkontos stimmt.*
- **AF-27:** *Als Verwalter möchte ich Einnahmen und Ausgaben einer Liegenschaft buchen, damit sie in der Eigentümerabrechnung erscheinen.*
- **AF-28:** *Als Verwalter möchte ich eine Eigentümerabrechnung erstellen, um den Auszahlungsbetrag an den Eigentümer zu ermitteln.*
- **AF-29:** *Als Verwalter möchte ich den Leerstand je Liegenschaft auswerten, um Mietausfälle zu erkennen.*

---

## 4. Weggelassene Technik und reine Lese-Ansichten (lückenlose Zuordnung aller 64 Routen)

Das Altsystem hat **64 Routen** (`bin/console debug:router`):
- **40 Routen** lösen fachliche Änderungen, Berechnungen oder Dokumente aus und bilden die **29 Anwendungsfälle**.
- **24 Routen** sind Technik oder reine Lese-Ansichten:

| Route | Art | Zuordnung |
|---|---|---|
| `app_login` | Technik | Anmeldung |
| `app_logout` | Technik | Abmeldung |
| `admin_users` | Technik | Reine Liste der Benutzer; Benutzer werden direkt in der Datenbank gepflegt (Ticket #145) |
| `dashboard` | Lese-Ansicht | Kennzahlen und anstehende Termine |
| `prospect_index` | Liste (Vermietung) | Einstieg für AF-01 bis AF-03 |
| `viewing_index` | Liste (Vermietung) | Einstieg für AF-02 |
| `tenancy_index` | Liste (Vermietung) | Einstieg für AF-05 bis AF-09 |
| `tenancy_show` | Detail (Vermietung) | Einstieg für AF-05 bis AF-09 |
| `owner_index` | Liste (Bestand) | Einstieg für AF-13 |
| `owner_show` | Detail (Bestand) | Eigentümer mit seinen Liegenschaften |
| `property_index` | Liste (Bestand) | Einstieg für AF-10 |
| `property_show` | Detail (Bestand) | Liegenschaft mit Gebäuden und Kennzahlen; Einstieg für AF-11 |
| `unit_index` | Liste (Bestand) | Einstieg für AF-12 |
| `unit_show` | Detail (Bestand) | Mieteinheit mit aktuellem Mietverhältnis; Einstieg für AF-04 und AF-16 |
| `tenant_index` | Liste (Bestand) | Einstieg für AF-14 |
| `tenant_show` | Detail (Bestand) | Mieter mit seinen Mietverhältnissen |
| `provider_index` | Liste (Bestand) | Einstieg für AF-15 |
| `issue_index` | Liste (Meldungen) | Einstieg für AF-16 und AF-17 (Sammeländerung) |
| `issue_show` | Detail (Meldungen) | Einstieg für AF-17 bis AF-20 |
| `task_index` | Liste (Aufgaben) | Einstieg für AF-21 bis AF-23 |
| `appointment_index` | Liste (Termine) | Einstieg für AF-24 |
| `charge_index` | Liste (Abrechnung) | Einstieg für AF-25 |
| `account_index` | Liste (Abrechnung) | Einstieg für AF-26 |
| `booking_index` | Liste (Abrechnung) | Einstieg für AF-27 |

**Abgleich:** 40 Routen in Anwendungsfällen + 24 Technik/Lese-Ansichten = 64 Routen. Jede Route kommt genau einmal vor.
