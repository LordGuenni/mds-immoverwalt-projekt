<?php

namespace App\DataFixtures;

use App\Entity\Appointment;
use App\Entity\Booking;
use App\Entity\Building;
use App\Entity\Charge;
use App\Entity\Handover;
use App\Entity\Issue;
use App\Entity\IssueStatusChange;
use App\Entity\Owner;
use App\Entity\Payment;
use App\Entity\Property;
use App\Entity\Prospect;
use App\Entity\RentalUnit;
use App\Entity\ServiceProvider;
use App\Entity\Task;
use App\Entity\Tenancy;
use App\Entity\Tenant;
use App\Entity\User;
use App\Entity\Viewing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Beispieldaten für Entwicklung und Lehre.
 */
class AppFixtures extends Fixture
{
    private $encoder;
    private $users = [];
    private $units = [];
    private $tenants = [];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $em)
    {
        $this->loadUsers($em);
        $owners = $this->loadOwners($em);
        $properties = $this->loadProperties($em, $owners);
        $providers = $this->loadProviders($em);
        $this->loadTenants($em);
        $em->flush();

        $tenancies = $this->loadTenancies($em);
        $em->flush();

        $this->loadLetting($em);
        $this->loadIssues($em, $properties, $providers);
        $this->loadAppointments($em, $properties, $tenancies);
        $this->loadAccounting($em, $properties, $tenancies);
        $em->flush();
    }

    // ------------------------------------------------------------------ Benutzer

    private function loadUsers(ObjectManager $em): void
    {
        $data = [
            ['admin', 'admin', 'Sabine Admin', ['ROLE_ADMIN']],
            ['verwalter', 'verwalter', 'Thomas Krüger', ['ROLE_PROPERTY_MANAGER']],
            ['vermieter', 'vermieter', 'Julia Brandt', ['ROLE_LETTING_MANAGER']],
        ];
        foreach ($data as [$username, $password, $name, $roles]) {
            $u = new User();
            $u->setUsername($username)->setFullName($name)->setRoles($roles)->setEmail($username.'@hausverwaltung.example');
            $u->setPassword($this->encoder->encodePassword($u, $password));
            $em->persist($u);
            $this->users[$username] = $u;
        }
    }

    // ------------------------------------------------------------------ Bestand

    private function loadOwners(ObjectManager $em): array
    {
        $data = [
            'muster' => ['EI-0001', 'Muster Immobilien GmbH', "Hauptstraße 1\n14776 Brandenburg an der Havel", 'Herr Muster', 'info@muster-immobilien.example', '03381 100200'],
            'havel' => ['EI-0002', 'Havelblick Grundbesitz KG', "Am Hafen 5\n14467 Potsdam", 'Frau Lindner', 'verwaltung@havelblick.example', '0331 445566'],
            'schulz' => ['EI-0003', 'Erbengemeinschaft Schulz', "Lindenweg 9\n14770 Brandenburg an der Havel", 'Herr Peter Schulz', null, '03381 778899'],
        ];
        $owners = [];
        foreach ($data as $key => [$nr, $name, $addr, $contact, $mail, $phone]) {
            $o = new Owner();
            $o->setOwnerNumber($nr)->setName($name)->setAddress($addr)->setContactPerson($contact)->setEmail($mail)->setPhone($phone);
            $em->persist($o);
            $owners[$key] = $o;
        }

        return $owners;
    }

    private function loadProperties(ObjectManager $em, array $owners): array
    {
        // key => [Nr, Name, Straße, Hausnr., PLZ, Ort, Eigentümer, Baujahr, Gebäude => [Name => [Etagen, Einheiten]]]
        $data = [
            'park' => ['OB-0101', 'Parkstraße 12', 'Parkstraße', '12', '14776', 'Brandenburg an der Havel', 'muster', 1996,
                ['Vorderhaus' => [4, 8], 'Hinterhaus' => [3, 4]]],
            'berlin' => ['OB-0102', 'Berliner Straße 42', 'Berliner Straße', '42', '14467', 'Potsdam', 'havel', 1911,
                ['Hauptgebäude' => [4, 8]]],
            'linden' => ['OB-0103', 'Lindenweg 9', 'Lindenweg', '9', '14770', 'Brandenburg an der Havel', 'schulz', 1965,
                ['Wohnhaus' => [2, 4]]],
            'markt' => ['OB-0104', 'Neustädtischer Markt 3', 'Neustädtischer Markt', '3', '14776', 'Brandenburg an der Havel', 'muster', 1890,
                ['Geschäftshaus' => [3, 5]]],
        ];

        $properties = [];
        $rent = ['park' => 11.2, 'berlin' => 11.4, 'linden' => 8.9, 'markt' => 13.5];
        foreach ($data as $key => [$nr, $name, $street, $hn, $zip, $city, $owner, $year, $buildings]) {
            $p = new Property();
            $p->setPropertyNumber($nr)->setName($name)->setStreet($street)->setHouseNumber($hn)->setZip($zip)->setCity($city)
                ->setOwner($owners[$owner])->setConstructionYear($year)->setManagementStart(new \DateTime('2015-01-01'));
            $em->persist($p);
            $properties[$key] = $p;

            $g = 0;
            foreach ($buildings as $bName => [$floors, $count]) {
                $g++;
                $b = new Building();
                $b->setBuildingNumber($nr.'-G'.$g)->setName($bName)->setAddress($street.' '.$hn.($g > 1 ? ' ('.$bName.')' : ''))
                    ->setFloors($floors)->setConstructionYear($year)->setProperty($p);
                $em->persist($b);
                $p->getBuildings()->add($b);

                for ($i = 0; $i < $count; $i++) {
                    $floor = intdiv($i, 2);
                    $side = $i % 2 === 0 ? 'links' : 'rechts';
                    $u = new RentalUnit();
                    $isShop = $key === 'markt' && $floor === 0;
                    $unitNr = ($isShop ? 'GE ' : 'WE ').sprintf('%02d', $i + 1).' '.$side;
                    if ($g > 1) {
                        $unitNr = 'HH '.sprintf('%02d', $i + 1).' '.$side;
                    }
                    $area = $isShop ? 95.0 + 10 * $i : 52.5 + 8.4 * (($i * 7) % 5);
                    $u->setUnitNumber($unitNr)->setBuilding($b)->setType($isShop ? 'GEW' : 'WHG')->setFloor($floor)
                        ->setArea(number_format($area, 2, '.', ''))
                        ->setColdRent(number_format(round($area * $rent[$key]), 2, '.', ''))
                        ->setServiceChargeAdvance(number_format(round($area * 2.6), 2, '.', ''));
                    $em->persist($u);
                    $b->getUnits()->add($u);
                    $this->units[$key.'-'.$g.'-'.($i + 1)] = $u;
                }
            }
        }

        // die bekannte Einheit aus der Vorlesung: Berliner Straße 42, WE 03 links, 78,20 m²
        $we03 = $this->units['berlin-1-3'];
        $we03->setArea('78.20')->setColdRent('890.00')->setServiceChargeAdvance('230.00');

        return $properties;
    }

    private function loadProviders(ObjectManager $em): array
    {
        $data = [
            'heizung' => ['Wärmetechnik Havel GmbH', 'HEIZUNG', 'Herr Kowalski', '03381 55501', 'ACTIVE'],
            'heizung_alt' => ['Heizung Schröder (Inh. W. Schröder)', 'HEIZUNG', 'Werner Schröder', '03381 20304', 'INACTIVE'],
            'sanitaer' => ['Sanitär Neumann', 'SANITAER', 'Frau Neumann', '03381 44422', 'ACTIVE'],
            'elektro' => ['Elektro Blitz & Sohn', 'ELEKTRO', null, '0331 909090', 'ACTIVE'],
            'maler' => ['Malerbetrieb Farbenfroh', 'MALER', 'Herr Weiß', '03381 13131', 'ACTIVE'],
            'hausmeister' => ['Hausmeisterservice Richter', 'HAUSMEISTER', 'Uwe Richter', '0171 1234567', 'ACTIVE'],
            'aufzug' => ['LiftTec Brandenburg', 'AUFZUG', 'Hotline', '0800 5438832', 'ACTIVE'],
            'reinigung' => ['Glanz & Co. Gebäudereinigung', 'REINIGUNG', 'Frau Yilmaz', '03381 67676', 'ACTIVE'],
        ];
        $providers = [];
        foreach ($data as $key => [$company, $trade, $contact, $phone, $status]) {
            $s = new ServiceProvider();
            $s->setCompany($company)->setTrade($trade)->setContactPerson($contact)->setPhone($phone)->setStatus($status);
            $em->persist($s);
            $providers[$key] = $s;
        }

        return $providers;
    }

    private function loadTenants(ObjectManager $em): void
    {
        $names = [
            ['Frau', 'Anna', 'Müller'], ['Herr', 'Jonas', 'Weber'], ['Frau', 'Lea', 'Weber'], ['Herr', 'Mehmet', 'Yıldız'],
            ['Frau', 'Katrin', 'Schmidt'], ['Herr', 'Paul', 'Hoffmann'], ['Frau', 'Sophie', 'Wagner'], ['Herr', 'Lukas', 'Becker'],
            ['Frau', 'Marie', 'Schulz'], ['Herr', 'Felix', 'Koch'], ['Frau', 'Emma', 'Richter'], ['Herr', 'Ben', 'Klein'],
            ['Frau', 'Hannah', 'Wolf'], ['Herr', 'Elias', 'Schröder'], ['Frau', 'Mia', 'Neumann'], ['Herr', 'Noah', 'Schwarz'],
            ['Frau', 'Lina', 'Zimmermann'], ['Herr', 'Tim', 'Braun'], ['Frau', 'Clara', 'Krüger'], ['Herr', 'David', 'Hofmann'],
            ['Frau', 'Laura', 'Hartmann'], ['Herr', 'Jan', 'Lange'], ['Frau', 'Nele', 'Schmitt'], ['Herr', 'Tom', 'Werner'],
            ['Frau', 'Ida', 'Krause'], ['Herr', 'Finn', 'Meier'],
        ];
        foreach ($names as $i => [$sal, $first, $last]) {
            $t = new Tenant();
            $t->setSalutation($sal)->setFirstName($first)->setLastName($last)
                ->setEmail(strtolower($first.'.'.str_replace(['ü', 'ö', 'ı'], ['ue', 'oe', 'i'], $last)).'@mail.example')
                ->setPhone('0151 '.(2000000 + 7391 * $i));
            $em->persist($t);
            $this->tenants[] = $t;
        }
        // Firmenmieter für Gewerbe
        foreach ([['Café Havelkorn', 'GbR'], ['Optik Scharf', 'e.K.']] as [$firm, $form]) {
            $t = new Tenant();
            $t->setFirstName($firm)->setLastName($form)->setNotes('Gewerbemieter');
            $em->persist($t);
            $this->tenants[] = $t;
        }
    }

    // ------------------------------------------------------------------ Vermietung

    private function loadTenancies(ObjectManager $em): array
    {
        // Einheit => [Mieter-Indizes, Beginn, Status, Kündigungsdatum]
        $plan = [
            'berlin-1-3' => [[0], '2023-03-01', Tenancy::ACTIVE, null],
            'berlin-1-1' => [[1, 2], '2019-08-01', Tenancy::ACTIVE, null],
            'berlin-1-2' => [[3], '2021-04-01', Tenancy::ACTIVE, null],
            'berlin-1-4' => [[4], '2017-10-01', Tenancy::NOTICE_GIVEN, '2026-08-15'],
            'berlin-1-5' => [[5], '2022-01-01', Tenancy::ACTIVE, null],
            'berlin-1-7' => [[6], '2020-06-01', Tenancy::ACTIVE, null],
            'park-1-1' => [[7], '2016-05-01', Tenancy::ACTIVE, null],
            'park-1-2' => [[8], '2018-09-01', Tenancy::ACTIVE, null],
            'park-1-3' => [[9], '2020-02-01', Tenancy::ACTIVE, null],
            'park-1-4' => [[10], '2021-11-01', Tenancy::ACTIVE, null],
            'park-1-5' => [[11], '2015-07-01', Tenancy::ACTIVE, null],
            'park-1-6' => [[12], '2024-03-01', Tenancy::ACTIVE, null],
            'park-1-8' => [[13], '2019-01-01', Tenancy::ACTIVE, null],
            'park-2-1' => [[14], '2023-06-01', Tenancy::ACTIVE, null],
            'park-2-2' => [[15], '2017-04-01', Tenancy::ACTIVE, null],
            'park-2-4' => [[16], '2025-09-01', Tenancy::ACTIVE, null],
            'linden-1-1' => [[17], '2012-03-01', Tenancy::ACTIVE, null],
            'linden-1-2' => [[18], '2019-12-01', Tenancy::ACTIVE, null],
            'linden-1-3' => [[19], '2021-08-01', Tenancy::ACTIVE, null],
            'markt-1-1' => [[26], '2018-04-01', Tenancy::ACTIVE, null],
            'markt-1-2' => [[27], '2022-10-01', Tenancy::ACTIVE, null],
            'markt-1-3' => [[20], '2020-05-01', Tenancy::ACTIVE, null],
            'markt-1-4' => [[21], '2024-08-01', Tenancy::ACTIVE, null],
        ];

        $tenancies = [];
        $n = 1000;
        foreach ($plan as $key => [$tenantIdx, $start, $status, $notice]) {
            $unit = $this->units[$key];
            $t = new Tenancy();
            $t->setContractNumber(sprintf('MV-%05d', ++$n))->setUnit($unit)->setStartDate(new \DateTime($start))
                ->setColdRent($unit->getColdRent())->setServiceChargeAdvance($unit->getServiceChargeAdvance())
                ->setDeposit(number_format(3 * (float) $unit->getColdRent(), 2, '.', ''))
                ->setStatus($status);
            foreach ($tenantIdx as $idx) {
                $t->addTenant($this->tenants[$idx]);
            }
            if ($notice) {
                $t->setNoticeDate(new \DateTime($notice));
                $t->setEndDate((new \DateTime($notice))->modify('+3 months'));
                // Flag wurde bei älteren Kündigungen nicht gepflegt
            }
            $unit->setStatus($status === Tenancy::NOTICE_GIVEN ? RentalUnit::NOTICE_GIVEN : RentalUnit::RENTED);
            $unit->setLastModifiedBy('import');
            $unit->getTenancies()->add($t);
            $em->persist($t);

            $h = new Handover();
            $h->setTenancy($t)->setType(Handover::MOVE_IN)->setDate(new \DateTime($start))->setKeyCount(4)
                ->setMeterReadings('Strom '.(10000 + $n * 7).', Kaltwasser '.(200 + $n % 50).', Warmwasser '.(100 + $n % 30));
            $em->persist($h);
            $tenancies[$key] = $t;
        }

        // Angebotsmiete der Einheit wurde nach Vertragsabschluss angehoben - der Vertrag behält den alten Wert
        $raised = $tenancies['park-1-1']->getUnit();
        $raised->setColdRent(number_format((float) $raised->getColdRent() * 1.12, 2, '.', ''));

        // früheres, beendetes Mietverhältnis in WE 03 links
        $old = new Tenancy();
        $old->setContractNumber('MV-00987')->setUnit($this->units['berlin-1-3'])->setStartDate(new \DateTime('2016-02-01'))
            ->setEndDate(new \DateTime('2023-01-31'))->setNoticeDate(new \DateTime('2022-10-28'))
            ->setColdRent('760.00')->setServiceChargeAdvance('210.00')->setDeposit('2280.00')
            ->setStatus(Tenancy::ENDED)->setNoticeGiven(true);
        $old->addTenant($this->tenants[22]);
        $em->persist($old);

        // Vertragsentwurf für eine reservierte Einheit
        $draftUnit = $this->units['linden-1-4'];
        $draftUnit->setStatus(RentalUnit::RESERVED);
        $draft = new Tenancy();
        $draft->setContractNumber('MV-01100')->setUnit($draftUnit)->setStartDate(new \DateTime('first day of next month'))
            ->setColdRent($draftUnit->getColdRent())->setServiceChargeAdvance($draftUnit->getServiceChargeAdvance())
            ->setDeposit(number_format(3 * (float) $draftUnit->getColdRent(), 2, '.', ''))->setStatus(Tenancy::DRAFT);
        $draft->addTenant($this->tenants[23]);
        $em->persist($draft);

        // Einheiten mit Sonderstatus
        $this->units['park-1-7']->setStatus(RentalUnit::RENOVATION);
        $this->units['berlin-1-8']->setStatus(RentalUnit::BLOCKED);
        $this->units['markt-1-5']->setShowInList(false);

        return $tenancies;
    }

    private function loadLetting(ObjectManager $em): void
    {
        $data = [
            ['Karin Vogel', 'berlin-1-6', '-20 days', 'VIEWING_PLANNED'],
            ['Moritz Seidel', 'berlin-1-6', '-12 days', 'NEW'],
            ['Familie Özdemir', 'park-2-3', '-30 days', 'APPLIED'],
            ['Greta Lorenz', 'park-2-3', '-8 days', 'NEW'],
            ['Nico Frank', 'markt-1-5', '-45 days', 'REJECTED'],
            ['Simone Haas', 'linden-1-4', '-60 days', 'ACCEPTED'],
        ];
        $prospects = [];
        foreach ($data as [$name, $unitKey, $date, $status]) {
            $p = new Prospect();
            $p->setName($name)->setEmail(strtolower(str_replace(' ', '.', $name)).'@mail.example')
                ->setUnit($this->units[$unitKey])->setRequestDate(new \DateTime($date))->setStatus($status);
            $em->persist($p);
            $prospects[] = $p;
        }

        $v = new Viewing();
        $v->setUnit($this->units['berlin-1-6'])->setProspect($prospects[0])->setDateTime(new \DateTime('+3 days 17:30'))
            ->setEmployee($this->users['vermieter']);
        $em->persist($v);

        $v2 = new Viewing();
        $v2->setUnit($this->units['park-2-3'])->setProspect($prospects[2])->setDateTime(new \DateTime('-10 days 16:00'))
            ->setEmployee($this->users['vermieter'])->setStatus('DONE')->setRemark('Familie mit 2 Kindern, sehr interessiert');
        $em->persist($v2);
    }

    // ------------------------------------------------------------------ Vorgänge

    private function loadIssues(ObjectManager $em, array $properties, array $providers): void
    {
        $verwalter = $this->users['verwalter'];
        $n = 2000;

        $mk = function (string $prop, ?string $unitKey, string $cat, string $desc, int $prio, string $status, string $created, ?string $notes = null) use ($em, $properties, $verwalter, &$n) {
            $i = new Issue();
            $i->setIssueNumber(sprintf('ME-%05d', ++$n))->setProperty($properties[$prop])->setCategory($cat)->setDescription($desc)
                ->setPriority($prio)->setStatus($status)->setAssignee($verwalter)->setCreatedAt(new \DateTime($created))
                ->setInternalNotes($notes);
            if ($unitKey) {
                $unit = $this->units[$unitKey];
                $i->setUnit($unit);
                $current = $unit->getCurrentTenancy();
                if ($current) {
                    $i->setTenant($current->getTenants()->first());
                }
            }
            $em->persist($i);
            $em->persist((new IssueStatusChange($i, null, 'NEW', 'verwalter', 'Meldung erfasst'))->setChangedAt(new \DateTime($created)));

            return $i;
        };

        $heating = $mk('berlin', 'berlin-1-3', 'HEIZUNG', 'Heizung funktioniert nicht, Heizkörper im Wohnzimmer bleibt kalt.', 3, 'IN_PROGRESS', '-4 days',
            'Mieterin ruft täglich an. Anlage ist Baujahr 1998 - Austausch mit Eigentümer besprechen.');
        $em->persist((new IssueStatusChange($heating, 'NEW', 'IN_PROGRESS', 'verwalter'))->setChangedAt(new \DateTime('-3 days')));

        $water = $mk('park', 'park-1-3', 'SANITAER', 'Wasser tritt unter der Spüle aus.', 4, 'RESOLVED', '-20 days');
        $window = $mk('park', 'park-1-5', 'FENSTER', 'Fenster im Schlafzimmer schließt nicht richtig.', 2, 'IN_REVIEW', '-6 days');
        $elevator = $mk('berlin', null, 'AUFZUG', 'Aufzug bleibt zwischen 2. und 3. OG stehen.', 4, 'IN_PROGRESS', '-2 days');
        $light = $mk('park', null, 'ELEKTRO', 'Licht im Treppenhaus Hinterhaus funktioniert nicht.', 2, 'NEW', '-1 days');
        $mould = $mk('linden', 'linden-1-2', 'SCHIMMEL', 'Schimmelverdacht im Bad an der Außenwand.', 3, 'IN_REVIEW', '-9 days',
            'Vermutlich Nutzerverhalten (Lüften). Gutachten nur auf Anweisung Eigentümer.');
        $mk('markt', 'markt-1-1', 'BESCHWERDE', 'Beschwerde über Lärm aus der Gaststätte im Nachbarhaus.', 1, 'REJECTED', '-40 days',
            'Nicht zuständig - Ordnungsamt.');
        $mk('berlin', 'berlin-1-1', 'SONSTIGES', 'Briefkastenschloss defekt.', 1, 'CLOSED', '-60 days');

        $t = 3000;
        $task = function (Issue $issue, string $title, ?ServiceProvider $provider, string $status, string $due) use ($em, $verwalter, &$t) {
            $task = new Task();
            $task->setTaskNumber(sprintf('AU-%05d', ++$t))->setTitle($title)->setIssue($issue)->setProperty($issue->getProperty())
                ->setUnit($issue->getUnit())->setResponsible($verwalter)->setServiceProvider($provider)
                ->setContactPerson($provider ? $provider->getContactPerson() : null)->setStatus($status)
                ->setDueDate(new \DateTime($due))->setPriority($issue->getPriority());
            $em->persist($task);
            $issue->getTasks()->add($task);

            return $task;
        };

        $task($heating, 'Hausmeister soll Anlage prüfen', $providers['hausmeister'], 'DONE', '-3 days');
        $task($heating, 'Heizungsfirma beauftragen', $providers['heizung'], 'ASSIGNED', '+1 days');
        $task($heating, 'Termin mit Mieterin abstimmen', null, 'OPEN', '+2 days');
        // Meldung ist "erledigt", aber eine Aufgabe ist noch offen
        $task($water, 'Siphon tauschen', $providers['sanitaer'], 'DONE', '-18 days');
        $task($water, 'Unterschrank trocknen lassen, Schaden begutachten', null, 'WAITING', '-5 days');
        $task($elevator, 'Störungsdienst Aufzug', $providers['aufzug'], 'IN_PROGRESS', '+0 days');
        // Aufgabe an inaktiven Dienstleister (Altfall)
        $task($mould, 'Feuchtigkeitsmessung', $providers['heizung_alt'], 'ASSIGNED', '+5 days');
        $task($window, 'Fenster einstellen', null, 'OPEN', '+7 days');

        // Aufgabe ohne Meldung
        $free = new Task();
        $free->setTaskNumber(sprintf('AU-%05d', ++$t))->setTitle('Rauchmelder Hinterhaus tauschen')->setProperty($properties['park'])
            ->setResponsible($verwalter)->setServiceProvider($providers['elektro'])->setStatus('OPEN')->setDueDate(new \DateTime('+21 days'));
        $em->persist($free);
    }

    private function loadAppointments(ObjectManager $em, array $properties, array $tenancies): void
    {
        $list = [
            ['Rauchmelderprüfung Parkstraße 12', 'RAUCHMELDER', '+9 days 08:00', 'PROPERTY', $properties['park']],
            ['Heizungswartung Berliner Straße 42', 'HEIZUNG', '+16 days 07:30', 'PROPERTY', $properties['berlin']],
            ['Zählerablesung gesamter Bestand', 'ZAEHLER', 'last day of december this year 09:00', 'ALL', null],
            ['Aufzugswartung', 'AUFZUG', '+30 days 10:00', 'PROPERTY', $properties['berlin']],
            ['Geplante Wasserabschaltung (Strangsanierung)', 'WASSER', '+12 days 08:00', 'BUILDING', $properties['park']->getBuildings()->first()],
            ['Handwerkertermin Heizung WE 03 links', 'HANDWERKER', '+1 days 10:00', 'UNIT', $this->units['berlin-1-3']],
            ['Wohnungsrückgabe (Auszug) WE 04 rechts', 'UEBERGABE', '2026-11-15 10:00', 'TENANCY', $tenancies['berlin-1-4']],
        ];
        foreach ($list as [$title, $type, $when, $refType, $ref]) {
            $a = new Appointment();
            $a->setTitle($title)->setType($type)->setStartsAt(new \DateTime($when))->setRefType($refType);
            if ($ref) {
                $em->flush(); // IDs werden für den Bezug benötigt
                $a->setRefId($ref->getId());
            }
            $em->persist($a);
        }
    }

    // ------------------------------------------------------------------ Abrechnung

    private function loadAccounting(ObjectManager $em, array $properties, array $tenancies): void
    {
        // Sollstellungen und Zahlungen Juli bis September 2026 für alle laufenden Verträge
        foreach ($tenancies as $key => $t) {
            foreach (['2026-07', '2026-08', '2026-09'] as $month) {
                $due = new \DateTime($month.'-03');
                $status = 'PAID';
                if ($key === 'berlin-1-3' && $month === '2026-08') {
                    $status = 'PARTIAL';
                }
                foreach (['COLD_RENT' => $t->getColdRent(), 'SERVICE_CHARGE' => $t->getServiceChargeAdvance()] as $pos => $amount) {
                    $c = new Charge();
                    $c->setTenancy($t)->setMonth($month)->setPosition($pos)->setDueDate($due)->setAmount($amount)
                        ->setStatus($pos === 'SERVICE_CHARGE' ? $status : 'PAID');
                    $em->persist($c);
                }

                $amount = $t->getTotalRent();
                if ($key === 'berlin-1-3' && $month === '2026-08') {
                    $amount = 1000.00; // Anna Müller zahlt im August nur 1.000 € statt 1.120 €
                }
                $p = new Payment();
                $p->setTenancy($t)->setPaymentDate(new \DateTime($month.'-02'))->setAmount(number_format($amount, 2, '.', ''))
                    ->setReference('Miete '.$month.' '.$t->getContractNumber());
                $em->persist($p);
            }
        }

        // Eigentümerabrechnung Parkstraße 12, August 2026 (Beispiel aus der Projektbeschreibung)
        $park = $properties['park'];
        $owner = $park->getOwner();
        $book = function (string $type, string $date, string $amount, string $cat, string $desc) use ($em, $park, $owner) {
            $b = new Booking();
            $b->setType($type)->setProperty($park)->setOwner($owner)->setDate(new \DateTime($date))->setAmount($amount)
                ->setCategory($cat)->setDescription($desc);
            $em->persist($b);
        };
        $book(Booking::INCOME, '2026-08-05', '8450.00', 'MIETE', 'Mieteinnahmen August (Sammelbuchung)');
        $book(Booking::EXPENSE, '2026-08-20', '420.00', 'HAUSMEISTER', 'Hausmeister August');
        $book(Booking::EXPENSE, '2026-08-24', '680.00', 'REPARATUR', 'Reparatur Heizung');
        $book(Booking::EXPENSE, '2026-08-31', '350.00', 'VERWALTUNG', 'Verwaltungsgebühr August');

        // Berliner Straße 42, September 2026 - Verwaltungsgebühr wird berechnet
        $berlin = $properties['berlin'];
        foreach ([['INCOME', '2026-09-04', '4720.00', 'MIETE', 'Mieteinnahmen September'], ['EXPENSE', '2026-09-15', '1240.00', 'WARTUNG', 'Aufzug Störungsdienst']] as [$type, $date, $amount, $cat, $desc]) {
            $b = new Booking();
            $b->setType($type)->setProperty($berlin)->setOwner($berlin->getOwner())->setDate(new \DateTime($date))
                ->setAmount($amount)->setCategory($cat)->setDescription($desc);
            $em->persist($b);
        }
    }
}
