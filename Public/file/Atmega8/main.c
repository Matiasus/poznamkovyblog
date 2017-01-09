#define F_CPU 8000000UL
#include <avr/io.h>
#include <avr/interrupt.h>
#include <util/delay.h>
#include <stdio.h>
#include <stdlib.h>
#include "lib/lcd.h"
#include "lib/pwm.h"

#define MODE 5
#define PRESCALER PRESCALER_1

#define ADC_CHANNEL(CHANNEL) { ADMUX = 0xF0; ADMUX &= CHANNEL; }

char to_lcd[16];

void pwm_init(void);
void int_init(void);
void adc_init(void);
void lcd_show(void);
uint16_t adc_read(uint8_t channel);

// Pridavanie striedy
ISR (INT0_vect)
{
  if (OCR1A < 251) {
    OCR1A = OCR1A + 5;
    lcd_show();
  }
}
// Uberanie striedy
ISR (INT1_vect)
{
  if (OCR1A > 4) {
    OCR1A = OCR1A - 5;
    lcd_show();  
  }
}

/***
 *  Hlavna funkcia
 *
 * @param Void
 * @return Void
 */
int main (void)
{
  // Inicializacia displeja
  lcd_init(LCD_DISP_ON);
  // spustenie pwm
  pwm_init();
  // spustenie povolenie preruseni od tlacidiel
  int_init();
  // inicializacia adc
  adc_init();
  // vymazanie obrazovky
  lcd_clrscr();
  // vypis textu
  lcd_puts(" START WITH PUSH");
  // povol globalne prerusenia 
  sei();   
  // nekonecna slucka
  while(1) {  

  }  
}

/***
 * Inicializacia PWM 
 * - vystup PB0 (IC1A)
 * - vyber modu 5, t.j. Fast PWM TOP = 255,
 * - nastavenie pociatocnej striedy PWM DUTY = 110/255
 *
 * @param Void
 * @return Void
 */
void pwm_init(void)
{
  // PB0 ako vystupny (OC1A)
  DDRB |= 0x02;

  // Nastavenie pociatocnej hodnoty PWM 110/255
  OCR1A = 110;

  // Nastavenie PWM modu a preddelicky
  pwm_select(MODE, PRESCALER);

  // Mazanie pri zhode TCNT1 a OCR1A, nastavenie pri BOTTOM
  TCCR1A |= (1 << COM1A1);
}

/***
 * Praca s prerusenim od tlacidiel 
 * - vstupy PD2 (INT0) a PD3 (INT1)
 * - aktivacia pull-up rezistorov na vstupe
 * - povolenie preruseni od vstupov
 *
 * @param Void
 * @return Void
 */
void int_init(void)
{
  // PD2 a PD3 ako vstupne piny
  DDRD &= ~((1 << PD2) | (1 << PD3));
 
  // Aktivovanie pull-up rezistora
  PORTD |= (1 << PD2) | (1 << PD3);

  // Povolenie prerusenia od 
  GICR |= (1 << INT0) | (1 << INT1);  

  // Dobezna hrana INT0 a INT1
  MCUCR |= (1 << ISC01) | (1 << ISC11);
}

/***
 * Analogovo digitalny prevodnik 
 * - vstup PC0 (ADC0)
 * - referencne napatie AVcc s externym kondenzatorom na AREF pine
 * - preddelicka 64 bity ADPS2:0 = 6 v ADCSRA, kvoli podmienke, ze 
 *   frekvencia prevodu ma byt v rozmedzi 50-200 kHz. Pri 8Mhz a preddelicke
 *   frekvancia prevodu je 125kHz
 *
 * @param Void
 * @return Void
 */
void adc_init(void)
{ 
  // referencne napatie AVcc s externym kondenzatorom na AREF pine 
  ADMUX |= (1 << REFS0);

  // nastavenie preddelicky 64
  ADCSRA |= (1 << ADPS2) | (1 << ADPS1);
}

/***
 * Citanie prevodu po ukonceni
 *
 * @param long - vstup pre prevodnik
 * @return uint16_t - hodnota prevodu
 */
uint16_t adc_read(uint8_t channel)
{
  // vstupny kanal PC0 (ADC0)
  ADC_CHANNEL(channel);
  // spustenie prevodu
  ADCSRA |= (1 << ADSC);
  // povolenie prevodu
  ADCSRA |= (1 << ADEN);
  // povolenie priznaku
  ADCSRA |= (1 << ADIF);
  // pocka pokial neukonci prevod
  while (!(ADCSRA & (1 << ADIF)));

  // vysledok
  return ADC;
}

/***
 * Zobrazenie na dispej
 *
 * @param void
 * @return void
 */
void lcd_show(void)
{
  int voltage;
  uint16_t adc;
  char duty[3];
  // citanie z AD prevodnika
  adc = adc_read(0);  
  // vypocet napatia v mV  
  voltage = (adc * 4.88);
  // strieda
  sprintf(duty, "%d", OCR1A);;
  // ak je strieda mensia ako 100
  if (OCR1A < 100 && OCR1A >= 10) {
    sprintf(duty, "0%d", OCR1A);
  // ak je strieda mensia ako 10
  } else if (OCR1A < 10) {
    sprintf(duty, "00%d", OCR1A);
  }
  // zapis do retazca
  sprintf(to_lcd," PWM:%s/255\nADC:%d %dmV", duty, adc, voltage);
  // vymzanie displeja
  lcd_clrscr();
  // zapis textu na lcd
  lcd_puts(to_lcd);
  // prerusenie 0,5s
  _delay_ms(500);
}
