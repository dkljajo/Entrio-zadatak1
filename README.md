# Entrio-zadatak1

# Enrio Tickets – PHP/MySQL Zadatak 1: 

Opis zadatka

Zadatak:  
Zamisli da postoje različite vrste ulaznica (npr. Early Bird, Regular, VIP) s različitim cijenama i kvotama.  
Cilj je napraviti sustav koji omogućava:

1. Svaka kategorija ulaznica ima svoju kvotu i cijenu.
2. Prodaja se automatski zatvara kada je kvota popunjena.
3. Organizator može promijeniti cijenu ili kvotu tijekom prodaje, bez gubitka podataka.
4. Svaka kupnja se bilježi u bazi (tablica `orders`).

---

## Rješenje

Projekt koristi:

- **PHP 8+** (backend)
- **MySQL** (baza podataka)
- **PDO + prepared statements** za sigurnu komunikaciju s bazom
- **Transakcije i `SELECT ... FOR UPDATE`** kako bi se spriječili race condition-i prilikom prodaje zadnjih ulaznica
- Jednostavni **HTML frontend** za kupnju i pregled statusa ulaznica

### Struktura baze podataka

#### `ticket_types`

| Polje | Tip                | Opis                       |
| ----- | ------------------ | -------------------------- |
| id    | INT AUTO_INCREMENT | Primarni ključ             |
| name  | VARCHAR(100)       | Naziv kategorije           |
| price | DECIMAL(10,2)      | Cijena ulaznice            |
| quota | INT                | Maksimalan broj ulaznica   |
| sold  | INT                | Broj već prodanih ulaznica |

#### `orders`

| Polje          | Tip                | Opis                    |
| -------------- | ------------------ | ----------------------- |
| id             | INT AUTO_INCREMENT | Primarni ključ          |
| ticket_type_id | INT                | FK na `ticket_types.id` |
| customer_name  | VARCHAR(100)       | Ime kupca               |
| customer_email | VARCHAR(255)       | Email kupca             |
| created_at     | TIMESTAMP          | Vrijeme kupnje          |



---

## Kako sustav radi

### 1 . Kupnja ulaznice (`buy_ticket.php`)

```php
$pdo->beginTransaction();
$stmt = $pdo->prepare("SELECT * FROM ticket_types WHERE name = ? FOR UPDATE");
$stmt->execute([$ticketType]);
$ticket = $stmt->fetch();
if ($ticket['sold'] >= $ticket['quota']) {
    throw new Exception("Kategorija '{$ticket['name']}' je rasprodana.");
}
$stmt = $pdo->prepare("UPDATE ticket_types SET sold = sold + 1 WHERE id = ?");
$stmt->execute([$ticket['id']]);
$stmt = $pdo->prepare("INSERT INTO orders (ticket_type_id, customer_name, customer_email) VALUES (?, ?, ?)");
$stmt->execute([$ticket['id'], $customerName, $customerEmail]);
$pdo->commit();
```

### 2.  Admin panel (`admin_update.php`)

```php
$fields = [];
$params = [];
if ($price !== null) { $fields[] = "price = ?"; $params[] = $price; }
if ($quota !== null) { $fields[] = "quota = ?"; $params[] = $quota; }
$params[] = $name;
$sql = "UPDATE ticket_types SET " . implode(", ", $fields) . " WHERE name = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
```

### 3.  Status ulaznica (`index.php`)

```php
$remaining = $t['quota'] - $t['sold'];
$status = $remaining > 0 ? $remaining : 'RASPRODANO';
```

---



---

## Sigurnost

- PDO + prepared statements → zaštita od SQL injection  
- Transakcije + row-level locking → sprječavanje race condition-a  
- `sold` se nikada ne resetira prilikom promjene cijene/kvote

---

## Struktura projekta

```
enrio_tickets/
├── db.php
├── index.php
├── buy_ticket.php
├── admin_update.php
└── sql_setup.sql
```

---

## Zaključak

Jednostavan sustav prodaje ulaznica s višestrukim kategorijama, automatskim zatvaranjem prodaje, mogućnošću promjene cijena/kvota i evidencijom svake kupnje. 
Preporucene nadogradnje: login sustav za admina, dashboard, prelazak na Laravel.
