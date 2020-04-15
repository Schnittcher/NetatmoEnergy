# Netatmo Energy Home
Über dieses Modul lassen sich Zeitpläne und Modi für das anze Haus einstellen.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Über dieses Modul lassen sich Zeitpläne und Modi für das anze Haus einstellen.

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.2

### 4. Einrichten der Instanzen in IP-Symcon

Über den Konfigurator kann diese Instanz angelegt werden.

__Konfigurationsseite__:

Keine Einstellungsmöglichkeiten vorhanden.

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
Modus|Integer| Über diese Variable kann der Modus für das ganze Haus eingestellt werden.
Zeitpläne|Integer| Über diese Variable kann ein Zeitplan für das ganze Haus aktiviert werden.

#### Profile

Name   | Typ
------ | -------
NA.HomeMode| Integer
NA.Schedules| Integer

### 6. WebFront

Über das Webfront können Zeitpläne und verschiedene Modi aktivert werden.

### 7. PHP-Befehlsreferenz

`RequestAction($VariablenID, $Value);`
Aktiviert einen Zeitplan oder Modi, Value ist in diesem Fall der Wert im Profil.

Beispiel:

`RequestAction(12345, 1);`

`RequestAction(12345, 2);`