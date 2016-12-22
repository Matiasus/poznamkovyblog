/****************************************************************************
 Title:       PWM library
 Author:      Marian Hrinko <mato.hrinko@gmail.com>
 File:	      pwm.h
 Date:        20/03/2015 
 Target:      Atmega8/8L/8L-8PU

 DESCRIPTION  Ovladanie PWM
 USAGE        Regulacia, komunikacia, ...
 ATTENTION    Po vybere rezimu a preddelicky je potrebne vysledovat dalsie 
              ovladanie/ nastavenie registrov OCR1A, OCR1B, IR1
*****************************************************************************/

/** @var Mozne kombinacie preddelicky hodin/taktu */
#define PRESCALER_0    0
#define PRESCALER_1    1
#define PRESCALER_8    2
#define PRESCALER_64   3
#define PRESCALER_256  4
#define PRESCALER_1024 5
#define PRESCALER_T1_F 6
#define PRESCALER_T1_R 7

/***
 * @popis   Vyber PWM modu
 *
 * @param   uint8_t mode -> 0 az 15
 * @return  void
 * *************************************************
 * TCCR1A = bit 1 | bit 0 |
 * TCCR1A = WGM11 | WGM10
 * -> maskovanie hodnotou 0x03 = 0x0000 0011 
 *
 * TCCR1B = bit 4 | bit 3 |
 * TCCR1B = WGM13 | WGM12
 * -> maskovanie hodnotou 0x0C = 0x0000 1100 a posuv
 *    o 1 poziciu v lavo
 * -------------------------------------------------
 * MODE  0 => WGM13=0 | WGM12=0 | WGM11=0 | WGM10=0
 * MODE  1 => WGM13=0 | WGM12=0 | WGM11=0 | WGM10=1
 * MODE  2 => WGM13=0 | WGM12=0 | WGM11=1 | WGM10=0
 * MODE  3 => WGM13=0 | WGM12=0 | WGM11=1 | WGM10=1
 * MODE  4 => WGM13=0 | WGM12=1 | WGM11=0 | WGM10=0
 * MODE  5 => WGM13=0 | WGM12=1 | WGM11=0 | WGM10=1
 * MODE  6 => WGM13=0 | WGM12=1 | WGM11=1 | WGM10=0
 * MODE  7 => WGM13=0 | WGM12=1 | WGM11=1 | WGM10=1
 * MODE  8 => WGM13=1 | WGM12=0 | WGM11=0 | WGM10=0
 * MODE  9 => WGM13=1 | WGM12=0 | WGM11=0 | WGM10=1
 * MODE 10 => WGM13=1 | WGM12=0 | WGM11=1 | WGM10=0
 * MODE 11 => WGM13=1 | WGM12=0 | WGM11=1 | WGM10=1
 * MODE 12 => WGM13=1 | WGM12=1 | WGM11=0 | WGM10=0
 * MODE 13 => WGM13=1 | WGM12=1 | WGM11=0 | WGM10=1
 * MODE 14 => WGM13=1 | WGM12=1 | WGM11=1 | WGM10=0
 * MODE 15 => WGM13=1 | WGM12=1 | WGM11=1 | WGM10=1
 * -------------------------------------------------
 */ 
#ifndef PWM_MODE
#define PWM_MODE(VALUE) { TCCR1A |= (0x03 & VALUE); TCCR1B |= ((0x0C & VALUE) << 1); }
#endif

/***
 * @popis   Vyber preddelicky hodin/taktu
 *
 * @param   uint8_t mode 0 az 7
 * @return  void
 * *************************************************
 * TCCR1B = bit2 | bit1 | bit0
 * TCCR1B = CS12 | CS11 | CS10
 * -> maskovanie hodnotou 0x07 = 0x0000 0111
 * -------------------------------------------------
 * Internal Clk/Prescaler
 * PRESCALER     0 => CS12=0 | CS11=0 | CS10=0
 * PRESCALER     1 => CS12=0 | CS11=0 | CS10=1
 * PRESCALER     8 => CS12=0 | CS11=1 | CS10=0
 * PRESCALER    64 => CS12=0 | CS11=1 | CS10=1
 * PRESCALER   256 => CS12=1 | CS11=0 | CS10=0
 * PRESCALER  1024 => CS12=1 | CS11=0 | CS10=1
 * -------------------------------------------------
 * External source
 * PRESCALER T1Pin => CS12=1 | CS11=1 | CS10=0 
 *                 => falling edge
 * PRESCALER T1Pin => CS12=1 | CS11=1 | CS10=1 
 *                 => rising edge
 */
#ifndef PWM_PRESCALER
#define PWM_PRESCALER(VALUE) { TCCR1B = (TCCR1B | (0x07 & VALUE)); }
#endif

/***
 * @popis   Inicializacia pwm
 *
 * @param   uint8_t mode - rezim pwm 0 az 15
 * @param   uint8_t prescaler - preddelicka 0 - 7
 * @return  voidI */
void pwm_select(int mode, int prescaler);
