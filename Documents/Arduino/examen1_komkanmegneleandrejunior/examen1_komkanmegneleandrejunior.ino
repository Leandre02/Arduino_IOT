#include <ArduinoJson.h>
#include <WiFiNINA.h>
#include <WiFiClient.h>
#include <ArduinoHttpClient.h>

#define PIN_BTN_ROUGE  14
#define PIN_BTN_JAUNE  15

/** Enregistre l'état de mes boutons et les transmet au pi
* @author : Leandre kanmegne
*/
char ssid[] = "Domotique-Pedago";
char pass[] = ""; // le mot de passe de mon wifi
char ip[]   = "172.19.240.244"; // l'adresse ip de mon pi 
int  port   = 80; // le port d'acces 


int status = WL_IDLE_STATUS;
WiFiClient wifi;
HttpClient client = HttpClient(wifi, ip, port);

bool dernierEtatBtnRouge = HIGH;
bool dernierEtatBtnJaune = HIGH;
int dernierMillisEtoile = 0;
int dernierMillisLED    = 0;
bool ledInternAllumee  = false;

const int INTERVALLE_ETOILE = 2000;
const int DUREE_LED         = 500;

void envoyerEtat(bool btnRouge, bool btnJaune) {
  JsonDocument doc;
  doc["bouton_rouge"] = (btnRouge == LOW);
  doc["bouton_jaune"] = (btnJaune == LOW);

  String donnees;
  serializeJson(doc, donnees);

  Serial.print("Envoi : ");
  Serial.println(donnees);

 client.post("/boutons.php", "application/json", donnees);

  int codeHttp   = client.responseStatusCode();
  String reponse = client.responseBody();

  Serial.print("Code HTTP : ");
  Serial.println(codeHttp);
  Serial.print("Reponse : ");
  Serial.println(reponse);
}

void setup() {
  Serial.begin(9600);
  while (!Serial);

  pinMode(LED_BUILTIN,  OUTPUT);
  pinMode(PIN_BTN_ROUGE, INPUT_PULLUP);
  pinMode(PIN_BTN_JAUNE, INPUT_PULLUP);

  while (status != WL_CONNECTED) {
    Serial.println("Connexion WiFi...");
    status = WiFi.begin(ssid, pass);
    delay(3000);
  }

  Serial.println("WiFi connecte !");
  Serial.print("Adresse IP de l'Arduino : ");
  Serial.println(WiFi.localIP());
}

void loop() {
 int maintenant = millis();

  // Affiche '*' sur le port toutes les 2 secondes
  if (maintenant - dernierMillisEtoile >= INTERVALLE_ETOILE) {
    Serial.println("*");
    dernierMillisEtoile = maintenant;
  }

  // Lecture des boutons
  bool lectureRouge = digitalRead(PIN_BTN_ROUGE);
  bool lectureJaune = digitalRead(PIN_BTN_JAUNE);

  // Si un etat a change
  if (lectureRouge != dernierEtatBtnRouge || lectureJaune != dernierEtatBtnJaune) {
    dernierEtatBtnRouge = lectureRouge;
    dernierEtatBtnJaune = lectureJaune;

    // Allume LED interne 500ms
    digitalWrite(LED_BUILTIN, HIGH);
    ledInternAllumee  = true;
    dernierMillisLED  = maintenant;

    // Envoie l'etat au serveur
    envoyerEtat(lectureRouge, lectureJaune);
  }

  // Eteint LED interne apres 500ms
  if (ledInternAllumee && maintenant - dernierMillisLED >= DUREE_LED) {
    digitalWrite(LED_BUILTIN, LOW);
    ledInternAllumee = false;
  }
}
