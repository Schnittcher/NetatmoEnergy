# Netatmo Energy Splitter
Ist für die Verbindung zwischen Netatmo Energy Cloud und den Geräten / Konfiguratoren zuständig.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
4. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
5. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Verbindet Netatmo Energy Cloud und die Geräte / Konfiguratoren miteinander
* Ruft in vorgegebenem Intervall den Status der Geräte ab

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.2

### 3. Einrichten der Instanzen in IP-Symcon

Der Splitter wird automatisch über den Configurator angelegt, sollte dieser noch nicht vorhanden sein.

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Home ID| Die ID des Homes, für welchen der Splitter verwendet werden soll.
Intervall| Gibt an in welchem Sekundentakt der Status abgefragt werden soll.

### 4. Statusvariablen und Profile

Keine Variablen und Profile vorhanden.

### 5. PHP-Befehlsreferenz

`NA_updateStatus($InstanceID);`
Ruft den Status ab und schickt diesen an alle Child Instanzen.

Beispiel:

`NA_updateStatus(12345);`

`NA_updateSchedules($InstanceID);`
Ruft die Zeitpläne ab und schickt diesen an die Child Instanz.

Beispiel:

`NA_updateSchedules(12345);`