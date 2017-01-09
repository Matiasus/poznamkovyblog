#ifndef F_CPU
#define F_CPU 16000000UL
#endif

#include <stdlib.h>
#include <avr/io.h>
#include <util/delay.h>
#include <avr/interrupt.h>
#include "lib/pcd8544.h"

// LCD max x-ova suradnica 
#define MAX_X 83
// LCD max y-ova suradnica
#define MAX_Y 47
// sirka (pocet zaznamenanych dat)
#define WIDTH 64
// vyska
#define HEIGHT 32
// krok na x ovej osi
#define STEP_X 8
// krok na y ovej osi
#define STEP_Y 8
// posuv x
#define OFFSET_X 9
// posuv y
#define OFFSET_Y 12
// @var Mozne kombinacie preddelicky hodin/taktu
#define PRESCALER_STOP   0
#define PRESCALER_1      1
#define PRESCALER_8      2
#define PRESCALER_64     3
#define PRESCALER_256    4
#define PRESCALER_1024   5
#define PRESCALER_T1_F   6
#define PRESCALER_T1_R   7
#define ADC_PRESCALER_16 4
#define ADC_PRESCALER_32 5
// Spustenie casovaca 0
// - nulovanie bitov preddelicky
// - nastavenie 
#define TIMER0_START(PRESCALER) { TCCR0 &= ~((1 << CS12) | (1 << CS11) | (1 << CS10)); TCCR0 |= PRESCALER & 0x07; }
// Spustenie casovaca 1A
// - nulovanie bitov preddelicky
// - nastavenie
#define TIMER1A_START(PRESCALER) { TCCR1B &= ~((1 << CS12) | (1 << CS11) | (1 << CS10)); TCCR1B |= PRESCALER & 0x07; }
// Vyber kanala
// - nulovanie poslednych troch bitov
// - nastavenie poslednych troch bitov podla kanala
#define ADC_CHANNEL(CHANNEL) { ADMUX &= 0xF8; ADMUX |= CHANNEL & 0x07; }
// Spustenie casovaca 1A
// - nulovanie bitov preddelicky
// - nastavenie
#define ADC_PRESCALER(PRESCALER) { ADCSRA &= ~((1 << ADPS2) | (1 << ADPS1) | (1 << ADPS0)); ADCSRA |= PRESCALER & 0x07; }

// pocitadlo
volatile uint8_t _index = 0;
// prerusenie
volatile uint8_t _request = 0;
// pole hodnot buffra
volatile uint8_t _buffer[WIDTH];

// inicializacia prerusenia
void Int0Init(void);
// inicializacia casovaca
void Timer1AInit(void);
// inicializacia casovaca
void Timer0Init(void);
// inicializacia prevodnika
void AdcInit(void);
// citanie hodnot z prevodnika
uint8_t AdcRead(void);
// zobrazenie osi
void AxisShow(void);
// zobrazenie hodnot buffra
void BufferShow(void);
// zobrazenie hodnot napatia
char VoltageShow(uint16_t, uint8_t, uint8_t);

/***
 * Prerusenie od externeho tlacidla
 *
 * @param INT0_vect
 * @return Void
 */
ISR(INT0_vect) 
{
  // kontorola maximalneho inkrementovania
  if (_request < 3) {
    // nuluj
    _request ++;
  } else {
    // nastavuj
    _request = 0;
  }
}

/***
 * Prerusenie od ukoncenia prevodu ADC
 *
 * @param ADC_vect
 * @return Void
 */
ISR(ADC_vect) 
{
  // nulovanie priznaku prerusenia po zhode TCNT0 a OCR1B
  // bez nulovania by prebehol prevod iba raz, pretoze by nedochadzalo 
  // k tvorbe nabeznej (spustacej) hrany (log 0 -> log 1) 
  TIFR |= (1 << OCF1B);
  // citanie z AD prevodnika s inkrementaciou
  _buffer[_index++] = AdcRead();
}
/***
 * Hlavna funkcia
 *
 * @param Void
 * @return Void
 */
int main(void)
{
  // inicializacia lcd
  Pcd8544Init();
  // inicializacia prerusenia
  Int0Init();
  // inicializacia casovaca 1A
  Timer1AInit();
  // inicializacia casovaca 0
  Timer0Init();
  // inicializacia adc
  AdcInit();
  // vyber kanala ADC
  ADC_CHANNEL(1);
  // Spustenie casovaca 0
  TIMER0_START(PRESCALER_8);
  // Spustenie casovac 1A
  TIMER1A_START(PRESCALER_1);
  // povolenie globalneho prerusenia
  sei();
  // nekonecna slucka
  while(1) {
    // zobrazenie a nulovanie po naplneni buffra
    if (_index > WIDTH) {
      // zobrazenie buffra
      BufferShow();
      // nulovanie
      _index = 0;
    }
  }
  // ukoncenie */
  return 0;
}

/***
 * Inicializacia prerusenia
 *
 * @param void
 * @return void
 */
void Int0Init(void)
{
  // pin PIN 2 ako vstupny
  DDRD  &= ~(1 << DDD2);
  // aktivovanie pull-up rezistora na vstupe
  PORTD |=  (1 << PORTD2);
  // prerusenie generovane zostupna hrana
  MCUCR |=  (1 << ISC01);
  // povolenie prerusenia od vstupu INT0 (PORTD PIN 2)
  GICR  |=  (1 << INT0);
}

/***
 * Inicializacia casovaca Timer1A
 *
 * @param void - number of seconds
 * @return void
 */
void Timer1AInit(void)
{
  // nuluje pocitadlo
  TCNT1  = 0;
  // foc1A = 1kHz:
  // ---------------------------------------------
  //   OCR1A = [fclk/(2.N.foc1A)] - 1
  //    fclk = 16 Mhz
  //       N = 1
  //   foc1A = 1/Toc1A
  OCR1A = 7999;
  // PIN PD5 - OC1A ako vystupny 
  DDRD  |= (1 << PD5);
  // Waveform generation - toggle
  TCCR1A |= (1 << COM1A0);
  // Mod CTC -> TOP = OCR1A
  TCCR1B |= (1 << WGM12);
}

/***
 * Inicializacia casovaca Timer0
 *
 * @param uint8_t - number of seconds
 * @return void
 */
void Timer0Init(void)
{
  // nuluje pocitadlo
  TCNT0  = 0;
  //    foc0 = 10kHz (20kHz):
  // ---------------------------------------------
  //    OCR0 = [fclk/(2.N.focnX)] - 1
  //    fclk = 16 Mhz
  //       N = 8
  //    foc0 = 1/Toc0
  // Pozn. 
  // Pri frekvencii 10kHz je pocitadlo dvakrat spustane, co znamena, 
  // ze priznak zhody OCR0 a TCNT0 -> OCF0 je 
  OCR0 = 99;
  // PIN PB3 - OC0 ako vystupny 
  // DDRB  |= (1 << PB3);
  // Waveform generation - toggle
  TCCR0 |= (1 << COM00);
  // Mod 2 - CTC -> TOP = OCRnX
  TCCR0 |= (1 << WGM01);
  // nulovanie priznaku zhody OCR0 a TCNT0
  // priznak sa nastavuje aj bez povolenia globalnych preruseni
  // a povoleni preruseni od zhody TCNT0 a OCR0
  TIFR  |= (1 << OCF0);
}

/***
 * Analogovo digitalny prevodnik 
 * - vstup PC0 (ADC0)
 * - referencne napatie AVcc s externym kondenzatorom na AREF pine
 * - preddelicka 128 bity ADPS2:0 = 8 v ADCSRA, kvoli podmienke, ze 
 *   frekvencia prevodu ma byt v rozmedzi 50-200 kHz. Pri 16Mhz a preddelicke
 *   frekvancia prevodu je 125kHz
 *
 * @param Void
 * @return Void
 */
void AdcInit(void)
{ 
  // referencne napatie AVcc s externym kondenzatorom na AREF pine 
  ADMUX |= (1 << REFS0);
  // zarovnanie do lava -> ADCH
  ADMUX |= (1 << ADLAR);
  // nastavenie prevodu
  ADCSRA |= (1 << ADIE)  | //povolenie prerusenia 
            (1 << ADEN)  | // povolenie prevodu 12 cyklov
            (1 << ADATE);
  // povolenie auto spustania
  // nastavenie preddelicky na 32
  // f = 16Mhz / 32 = 500 kHz Prevod => 27us
  ADC_PRESCALER(ADC_PRESCALER_32); 
  // Timer/Counter0 Compare Match
  SFIOR |= (1 << ADTS1) | (1 << ADTS0);
}

/***
 * Citanie prevodu po ukonceni
 *
 * @param long - vstup pre prevodnik
 * @return uint16_t - hodnota prevodu
 */
uint8_t AdcRead(void)
{
  // vysledok
  return ADCH;
}

/***
 * Zobrazenie hodnot na lcd
 *
 * @param void
 * @return void
 */
void BufferShow()
{
  uint8_t i;
  uint8_t min = 127;
  uint8_t max = 0;
  uint16_t sum = 0;

  // zakazanie preruseni
  cli();
  // vymazanie obrazovky
  ClearScreen();
  // vykreslenie osi
  AxisShow();
  // zobrazenie nabuffrovanych hodnot
  for (i=0; i<WIDTH; i++) {
    // zapis do retazca
    DrawLine(i+OFFSET_X, i+OFFSET_X+1, HEIGHT+OFFSET_Y-HEIGHT*_buffer[i]/255, HEIGHT+OFFSET_Y-HEIGHT*_buffer[i+1]/255);
    // accumulator
    sum += _buffer[i];
    // overenie minima
    if (_buffer[i] < min) {
      // nova hodnota
      min = _buffer[i];
    }
    // overenie maxima
    if (_buffer[i] > max) {
      // nova hodnota
      max = _buffer[i];
    }
  }
  // vypis napatia
  VoltageShow(sum, min, max);
  // zobrazenie */
  UpdateScreen();
  // vyckanie 1 s
  _delay_ms(1500);
  // povolenie preruseni
  sei();
}

/***
 * Zobrazenie hodnot na lcd
 *
 * @param uint16_t
 * @param uint8_t
 * @param uint8_t
 * @return void
 */
char VoltageShow(uint16_t sum, uint8_t min, uint8_t max)
{
  char voltage[4];
  // potreba vykreslenia?
  if (_request == 0) {
    // navrat
    return 0;
  }
  // nastavenie na 0, 0
  SetTextPosition(0, 0);
  // potreba vykreslenia?
  if (_request == 1) {
    // vypis textu
    DrawString("Ustr[mV]: ");
    // konvertovanie do textu
    itoa((int) ((5000*((long) sum/83))/255), voltage, 10);
  }
  // potreba vykreslenia?
  if (_request == 2) {
    // vypis textu
    DrawString("Umin[mV]: ");
    // konvertovanie do textu
    itoa((int) 5000*((long) min/255), voltage, 10);
  }
  // potreba vykreslenia?
  if (_request == 3) {
    // vypis textu
    DrawString("Umax[mV]: ");
    // konvertovanie do textu
    itoa((int) 5000*((long) max/255), voltage, 10);
  }
  // nastavenie na 0, 6
  SetTextPosition(0, 10);
  // vypis napatia
  DrawString(voltage);

  return 0;
}

/***
 * Zobrazenie pomocnych osi
 *
 * @param void
 * @return void
 */
void AxisShow()
{
  uint8_t i = OFFSET_X;
  // vykreslenie hlavnej osi x
  DrawLine(OFFSET_X-2, OFFSET_X+WIDTH+1, HEIGHT+OFFSET_Y, HEIGHT+OFFSET_Y);
  // vykreslovanie pomocnej x-ovej osi
  while (i <= WIDTH+OFFSET_X) {
    // vykreslenie bodov na x-ovej osi
    DrawLine(i, i, HEIGHT+OFFSET_Y, HEIGHT+OFFSET_Y+2);
    // posuv o 1 doprava
    i += STEP_X;
  }
  i = OFFSET_Y;
  // vykreslenie hlavnej osi y
  DrawLine(OFFSET_X, OFFSET_X, OFFSET_Y-1, HEIGHT+OFFSET_Y+2);
  // vykreslovanie pomocnej x-ovej osi
  while (i <= HEIGHT+OFFSET_Y) {
    // vykreslenie bodov na x-ovej osi
    DrawLine(OFFSET_X-2, OFFSET_X, i, i);
    // posuv o 1 doprava
    i += STEP_Y;
  }
}
