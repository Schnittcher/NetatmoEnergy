# Netatmo Energy Device
Visualisiert Werte von Netatmo Energy Geräten wie Batterie Status, Signalstärke usw. in IP-Symcon.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Visualisieren von Werten in IP-Symcon

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.2

### 4. Einrichten der Instanzen in IP-Symcon

Über den Konfigurator kann diese Instanz angelegt werden.

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Module ID| Die ID des Gerätes, wird beim Anlegen über den Konfigurator gefüllt.

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
Batterie Level|Integer| Zeigt das Level der Batterie an.
Batterie Status|String| Zeigt den Status der Batterie an.
Bridge|String| Die Variable zeigt an, mit welcher Bridge das Gerät verbunden ist.
Firmware Revision|Integer| Zeigt die Firmware Version des Gerätes an.
RF Stärke|Integer| Zeigt die Signalstärke des Gerätes.
Wifi Stärke|Integer| Zeigt die Wlan Signalstärke des Gerätes.

#### Profile

Keine Vorhanden.

### 6. WebFront

Über das Webfront können die Statuswerte eingesehen werden.

### 7. PHP-Befehlsreferenz

Keine Funktionen vorhanden.