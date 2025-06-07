# Dokument wymagań produktu (PRD) - WP Blueprint Creator

## 1. Przegląd produktu

WP Blueprint Creator to webowa aplikacja umożliwiająca tworzenie stron demonstracyjnych dla WordPressa z wykorzystaniem WordPress Playground. Aplikacja pozwala użytkownikom na szybkie i łatwe tworzenie konfiguracji (blueprintów) dla środowiska WordPress, które mogą być natychmiast uruchomione w przeglądarce bez konieczności instalacji na serwerze.

Główne cechy produktu:
- Tworzenie konfiguracji WordPress (blueprintów) poprzez intuicyjny interfejs graficzny
- Wybór wersji PHP oraz WordPressa
- Instalacja wtyczek i motywów z repozytorium WordPress lub poprzez URL
- Możliwość tworzenia demonstracji bez konieczności rejestracji
- Generowanie linków do gotowych demonstracji
- Zarządzanie własną biblioteką blueprintów po rejestracji

Produkt jest skierowany do deweloperów wtyczek i motywów WordPressa, firm oferujących rozwiązania dla WordPressa oraz edukatorów, którzy potrzebują szybko tworzyć środowiska demonstracyjne.

## 2. Problem użytkownika

Użytkownicy, szczególnie deweloperzy wtyczek i motywów WordPressa, napotykają na następujące problemy:

1. Czasochłonny proces demonstrowania produktów potencjalnym klientom - wymaga od klientów instalacji WordPressa, konfiguracji środowiska, instalacji wtyczek/motywów.

2. Brak możliwości szybkiego przekazania działającego dema WordPress bez udostępniania dostępu do własnych serwerów.

3. Konieczność utrzymywania wielu instancji WordPress do celów demonstracyjnych, co generuje koszty.

4. Skomplikowany proces tworzenia konfiguracji dla WordPress Playground, który wymaga znajomości formatu JSON i specyfikacji blueprintów.

5. Brak centralnego miejsca do zarządzania i udostępniania konfiguracji demonstracyjnych.

WP Blueprint Creator rozwiązuje te problemy, dostarczając prosty interfejs do tworzenia konfiguracji WordPress, które mogą być natychmiast uruchomione w przeglądarce i udostępniane poprzez link.

## 3. Wymagania funkcjonalne

### 3.1 Tworzenie Blueprintów

- Jednostronicowy formularz z polami pogrupowanymi w accordiony dla przejrzystej organizacji
- Pola obowiązkowe:
  - Nazwa blueprintu (pole tekstowe)
  - Wersja PHP (select, opcje od 7.4 do 8.3)
  - Wersja WordPressa (select, opcje od 6.0 do najnowszej wersji oraz wersji beta)
- Pola nieobowiązkowe:
  - Kroki instalacji (pole powtarzalne)
- Dynamiczne zmiany formularza w zależności od wyboru kroku (instalacja wtyczki/motywu)
- Dla wtyczek możliwość:
  - Wyboru z repozytorium WordPress
  - Podania URL do pliku ZIP
- Generowanie blueprintu w formacie JSON
- Zapisywanie blueprintu w bazie danych
- Tworzenie unikalnego identyfikatora dla blueprintu

### 3.2 Zarządzanie Użytkownikami

- Możliwość tworzenia blueprintów jako anonimowy użytkownik
- System rejestracji użytkowników z wykorzystaniem adresu email i hasła
- Logowanie użytkowników
- Zarządzanie własnymi blueprintami:
  - Edycja
  - Usuwanie
  - Dodawanie nowych
- Podstawowe funkcje zarządzania kontem:
  - Zmiana hasła
  - Resetowanie hasła

### 3.3 Integracja z WordPress Playground

- Generowanie blueprintów w formacie JSON kompatybilnym z WordPress Playground
- Przekierowanie do WordPress Playground z parametrem blueprint-url
- Możliwość kopiowania linku do blueprintu
- Uruchamianie demo bezpośrednio z aplikacji

### 3.4 Przeglądanie Blueprintów

- Strona główna prezentująca popularne blueprinty
- Lista własnych blueprintów użytkownika posortowana według daty utworzenia
- Wyszukiwanie i filtrowanie blueprintów
- Wyświetlanie statystyk dla blueprintów:
  - Liczba wyświetleń
  - Liczba uruchomień

### 3.5 Wymagania Techniczne

- Backend: Laravel
- Frontend: Astro + React
- Baza danych: MySQL lub MariaDB
- Responsywny design z podejściem mobile-first
- Dokumentacja w formie osadzonego filmu z YouTube
- Obsługa 100 jednoczesnych użytkowników

## 4. Granice produktu

Następujące funkcjonalności NIE są częścią MVP (Minimum Viable Product):

1. Konta premium z dodatkowymi funkcjonalnościami
2. Duplikowanie istniejących blueprintów
3. Współdzielenie kolekcji blueprintów między użytkownikami
4. Integracje z innymi platformami edukacyjnymi
5. Aplikacje mobilne (na początek tylko wersja webowa)
6. Prywatne blueprinty (wszystkie blueprinty w MVP są publiczne)
7. Zaawansowana analityka użycia blueprintów
8. System komentarzy lub ocen blueprintów
9. Wersjonowanie blueprintów
10. System powiadomień dla użytkowników

## 5. Historyjki użytkowników

### Anonimowy Użytkownik

#### US-001: Tworzenie blueprintu
**Opis:** Jako niezalogowany użytkownik, chcę stworzyć blueprint dla WordPressa, aby zademonstrować działanie mojej wtyczki lub motywu.

**Kryteria akceptacji:**
- Użytkownik może otworzyć formularz tworzenia blueprintu bez logowania
- Formularz zawiera wszystkie niezbędne pola (nazwa, wersja PHP, wersja WP)
- Użytkownik może dodawać kroki instalacji (wtyczek/motywów)
- System generuje unikalny identyfikator dla blueprintu
- Po zapisaniu blueprint jest dostępny pod wygenerowanym linkiem

#### US-002: Kopiowanie linku do blueprintu
**Opis:** Jako niezalogowany użytkownik, chcę skopiować link do mojego blueprintu, aby udostępnić go innym osobom.

**Kryteria akceptacji:**
- Po utworzeniu blueprintu system wyświetla link do niego
- Obok linku znajduje się przycisk do szybkiego kopiowania
- Po kliknięciu przycisku "Kopiuj" link jest kopiowany do schowka
- System wyświetla potwierdzenie skopiowania linku

#### US-003: Uruchamianie blueprintu w WordPress Playground
**Opis:** Jako niezalogowany użytkownik, chcę uruchomić utworzony blueprint w WordPress Playground, aby przetestować jego działanie.

**Kryteria akceptacji:**
- Na stronie szczegółów blueprintu jest przycisk "Uruchom w WordPress Playground"
- Po kliknięciu przycisku użytkownik jest przekierowany do WordPress Playground z odpowiednim parametrem blueprint-url
- WordPress Playground poprawnie ładuje konfigurację z blueprintu
- System rejestruje uruchomienie jako statystykę

#### US-004: Przeglądanie popularnych blueprintów
**Opis:** Jako niezalogowany użytkownik, chcę przeglądać popularne blueprinty, aby znaleźć przydatne konfiguracje WordPressa.

**Kryteria akceptacji:**
- Strona główna wyświetla listę popularnych blueprintów
- Każdy blueprint na liście zawiera nazwę, liczbę wyświetleń i liczbę uruchomień
- Użytkownik może kliknąć na blueprint, aby zobaczyć jego szczegóły
- Użytkownik może filtrować/sortować listę według różnych kryteriów

### Zarejestrowany Użytkownik

#### US-005: Rejestracja konta
**Opis:** Jako użytkownik, chcę zarejestrować konto, aby móc zarządzać swoimi blueprintami.

**Kryteria akceptacji:**
- Formularz rejestracji wymaga podania adresu email i hasła
- System weryfikuje poprawność adresu email
- System weryfikuje siłę hasła
- Po rejestracji użytkownik otrzymuje email z potwierdzeniem
- Po rejestracji użytkownik jest automatycznie zalogowany

#### US-006: Logowanie do systemu
**Opis:** Jako zarejestrowany użytkownik, chcę zalogować się do systemu, aby uzyskać dostęp do swoich blueprintów.

**Kryteria akceptacji:**
- Formularz logowania wymaga podania adresu email i hasła
- System weryfikuje poprawność podanych danych
- W przypadku błędu system wyświetla odpowiedni komunikat
- Po poprawnym logowaniu użytkownik jest przekierowany do swojego panelu
- Istnieje opcja "Zapamiętaj mnie" przedłużająca sesję

#### US-007: Zarządzanie własnymi blueprintami
**Opis:** Jako zalogowany użytkownik, chcę zarządzać swoimi blueprintami, aby móc je edytować lub usuwać.

**Kryteria akceptacji:**
- Panel użytkownika zawiera listę wszystkich jego blueprintów
- Przy każdym blueprincie są przyciski "Edytuj" i "Usuń"
- Po kliknięciu "Edytuj" użytkownik jest przekierowany do formularza edycji
- Po kliknięciu "Usuń" system wyświetla pytanie potwierdzające
- Po potwierdzeniu usunięcia blueprint jest trwale usuwany z systemu

#### US-008: Przeglądanie własnych blueprintów
**Opis:** Jako zalogowany użytkownik, chcę przeglądać listę swoich blueprintów, aby znaleźć wcześniej utworzone demo.

**Kryteria akceptacji:**
- Panel użytkownika wyświetla listę wszystkich jego blueprintów
- Lista jest sortowana domyślnie według daty utworzenia (od najnowszych)
- Użytkownik może wyszukiwać wśród swoich blueprintów
- Każdy blueprint na liście zawiera nazwę, datę utworzenia i statystyki
- Użytkownik może zmienić sposób sortowania listy

#### US-009: Zmiana hasła
**Opis:** Jako zalogowany użytkownik, chcę zmienić swoje hasło, aby zwiększyć bezpieczeństwo mojego konta.

**Kryteria akceptacji:**
- W ustawieniach konta jest opcja zmiany hasła
- Formularz zmiany hasła wymaga podania aktualnego hasła
- Formularz wymaga podania nowego hasła i jego potwierdzenia
- System weryfikuje siłę nowego hasła
- System wyświetla potwierdzenie po zmianie hasła

### Deweloper/Firma

#### US-010: Tworzenie demo wtyczki
**Opis:** Jako deweloper wtyczki, chcę stworzyć demo mojej wtyczki, aby potencjalni klienci mogli ją wypróbować bez instalacji.

**Kryteria akceptacji:**
- Formularz tworzenia blueprintu pozwala na dodanie kroku instalacji wtyczki
- Użytkownik może wybrać wtyczkę z repozytorium WordPress
- Użytkownik może podać URL do pliku ZIP z wtyczką
- System poprawnie dodaje wtyczkę do konfiguracji WordPress
- Po uruchomieniu w WordPress Playground wtyczka jest zainstalowana i aktywowana

#### US-011: Tworzenie demo motywu
**Opis:** Jako deweloper motywu, chcę stworzyć demo mojego motywu, aby pokazać jego funkcjonalności potencjalnym klientom.

**Kryteria akceptacji:**
- Formularz tworzenia blueprintu pozwala na dodanie kroku instalacji motywu
- Użytkownik może wybrać motyw z repozytorium WordPress
- Użytkownik może podać URL do pliku ZIP z motywem
- System poprawnie dodaje motyw do konfiguracji WordPress
- Po uruchomieniu w WordPress Playground motyw jest zainstalowany i aktywowany

### Edukator

#### US-012: Tworzenie konfiguracji edukacyjnej
**Opis:** Jako edukator, chcę stworzyć różne konfiguracje WordPressa, aby wykorzystać je w materiałach szkoleniowych.

**Kryteria akceptacji:**
- Formularz tworzenia blueprintu pozwala na szczegółową konfigurację środowiska
- Użytkownik może dodać wiele wtyczek i motywów
- Użytkownik może określić wersję PHP i WordPressa odpowiednią do celów edukacyjnych
- Wygenerowany link jest stabilny i działa długoterminowo
- System umożliwia dodanie opisu edukacyjnego do blueprintu

### Bezpieczeństwo i Wydajność

#### US-013: Bezpieczne uwierzytelnianie
**Opis:** Jako użytkownik systemu, chcę mieć pewność, że moje dane uwierzytelniające są bezpiecznie przechowywane i przetwarzane.

**Kryteria akceptacji:**
- Hasła są hashowane przy użyciu bezpiecznych algorytmów
- System wymusza minimalne wymagania dotyczące siły hasła
- Sesje użytkowników mają ograniczony czas trwania
- System oferuje opcję resetowania hasła poprzez email
- System blokuje konto po kilku nieudanych próbach logowania

#### US-014: Odporność na obciążenie
**Opis:** Jako użytkownik systemu, chcę korzystać z aplikacji bez opóźnień nawet przy dużym obciążeniu serwera.

**Kryteria akceptacji:**
- System obsługuje co najmniej 100 jednoczesnych użytkowników
- Czas odpowiedzi dla podstawowych operacji nie przekracza 2 sekund
- System posiada mechanizmy cache'owania, aby zmniejszyć obciążenie bazy danych
- System monitoruje wydajność i automatycznie dostosowuje zasoby
- W przypadku zwiększonego obciążenia system utrzymuje funkcjonalność krytycznych elementów

## 6. Metryki sukcesu

### 6.1 Metryki produktowe
1. Użytkownicy mogą samodzielnie tworzyć działające dema WordPressa i uruchamiać je w WordPress Playground
2. Wszystkie blueprinty są poprawnie zapisywane w bazie danych i dostępne przez wygenerowane linki
3. Użytkownicy mogą kopiować i udostępniać linki do blueprintów w formacie JSON
4. System umożliwia zarządzanie własnymi blueprintami po zalogowaniu

### 6.2 Metryki techniczne
1. System obsługuje 100 jednoczesnych użytkowników bez zauważalnego spadku wydajności
2. Aplikacja w pełni działa na urządzeniach mobilnych (responsywność)
3. Czas ładowania stron nie przekracza 3 sekund
4. Okres testów przed oficjalnym uruchomieniem wynosi 1 tydzień bez krytycznych błędów

### 6.3 Metryki biznesowe
1. Aktywnych użytkowników (miesięcznie): cel do ustalenia po fazie MVP
2. Liczba utworzonych blueprintów (miesięcznie): cel do ustalenia po fazie MVP
3. Liczba uruchomień blueprintów (miesięcznie): cel do ustalenia po fazie MVP
4. Odsetek powracających użytkowników: cel do ustalenia po fazie MVP

### 6.4 Sukces MVP
MVP zostanie uznane za sukces, jeśli spełni następujące kryteria:
1. Co najmniej 90% użytkowników testowych jest w stanie samodzielnie utworzyć blueprint i uruchomić go w WordPress Playground
2. Średni czas potrzebny na utworzenie blueprintu nie przekracza 5 minut
3. System działa stabilnie przez 48 godzin ciągłego testowania
4. Nie występują krytyczne błędy bezpieczeństwa